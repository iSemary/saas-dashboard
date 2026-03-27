"use client"

import { useState, useEffect } from "react"
import { useParams, useRouter } from "next/navigation"
import DOMPurify from "dompurify"
import { Button } from "@/components/ui/button"
import { Badge } from "@/components/ui/badge"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs"
import { Textarea } from "@/components/ui/textarea"
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select"
import {
  getTicket,
  getTicketComments,
  createComment,
  updateTicketStatus,
  assignTicket,
  type Ticket,
  type Comment,
} from "@/lib/tickets"
import { ArrowLeft, Send, User, Clock } from "lucide-react"
import { toast } from "sonner"
import { format, formatDistanceToNow } from "date-fns"
import Link from "next/link"
import { useAuth } from "@/context/auth-context"

export default function TicketDetailPage() {
  const params = useParams()
  const router = useRouter()
  const { user } = useAuth()
  const ticketId = Number(params.id)
  const [ticket, setTicket] = useState<Ticket | null>(null)
  const [comments, setComments] = useState<Comment[]>([])
  const [loading, setLoading] = useState(true)
  const [commentText, setCommentText] = useState("")
  const [submitting, setSubmitting] = useState(false)

  useEffect(() => {
    if (ticketId) {
      loadTicket()
      loadComments()
    }
  }, [ticketId])

  const loadTicket = async () => {
    try {
      setLoading(true)
      const response = await getTicket(ticketId)
      setTicket(response.data)
    } catch (error: any) {
      toast.error("Failed to load ticket")
      console.error(error)
      router.push("/dashboard/tickets")
    } finally {
      setLoading(false)
    }
  }

  const loadComments = async () => {
    try {
      const response = await getTicketComments(ticketId)
      setComments(response.data)
    } catch (error) {
      console.error("Failed to load comments", error)
    }
  }

  const handleAddComment = async () => {
    if (!commentText.trim()) return

    try {
      setSubmitting(true)
      await createComment({
        comment: commentText,
        object_id: ticketId,
        object_model: "Modules\\Ticket\\Entities\\Ticket",
      })
      setCommentText("")
      toast.success("Comment added")
      loadComments()
    } catch (error: any) {
      toast.error("Failed to add comment")
      console.error(error)
    } finally {
      setSubmitting(false)
    }
  }

  const handleStatusChange = async (status: string) => {
    try {
      await updateTicketStatus(ticketId, status)
      toast.success("Status updated")
      loadTicket()
    } catch (error: any) {
      toast.error("Failed to update status")
      console.error(error)
    }
  }

  if (loading) {
    return <div className="text-center py-12 text-muted-foreground">Loading...</div>
  }

  if (!ticket) {
    return null
  }

  const getStatusColor = (status: string) => {
    const colors: Record<string, string> = {
      open: "bg-blue-100 text-blue-800",
      in_progress: "bg-yellow-100 text-yellow-800",
      on_hold: "bg-gray-100 text-gray-800",
      resolved: "bg-green-100 text-green-800",
      closed: "bg-gray-100 text-gray-800",
    }
    return colors[status] || "bg-gray-100 text-gray-800"
  }

  const getPriorityColor = (priority: string) => {
    const colors: Record<string, string> = {
      low: "bg-green-100 text-green-800",
      medium: "bg-blue-100 text-blue-800",
      high: "bg-orange-100 text-orange-800",
      urgent: "bg-red-100 text-red-800",
    }
    return colors[priority] || "bg-gray-100 text-gray-800"
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center gap-4">
        <Link href="/dashboard/tickets">
          <Button variant="ghost" size="icon">
            <ArrowLeft className="h-4 w-4" />
          </Button>
        </Link>
        <div className="flex-1">
          <h1 className="text-3xl font-bold">{ticket.title}</h1>
          <p className="text-muted-foreground">Ticket #{ticket.ticket_number}</p>
        </div>
        <div className="flex items-center gap-2">
          <Badge className={getStatusColor(ticket.status)}>
            {ticket.status.replace("_", " ")}
          </Badge>
          <Badge className={getPriorityColor(ticket.priority)}>
            {ticket.priority}
          </Badge>
        </div>
      </div>

      <div className="grid grid-cols-3 gap-6">
        <Card className="col-span-2">
          <CardHeader>
            <CardTitle>Details</CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <div>
              <h3 className="font-semibold mb-2">Description</h3>
              <p className="text-sm text-muted-foreground whitespace-pre-wrap">
                {ticket.description}
              </p>
            </div>

            {ticket.html_content && (
              <div>
                <h3 className="font-semibold mb-2">Content</h3>
                <div
                  className="text-sm text-muted-foreground"
                  dangerouslySetInnerHTML={{ 
                    __html: DOMPurify.sanitize(ticket.html_content) 
                  }}
                />
              </div>
            )}

            <div className="pt-4 border-t">
              <h3 className="font-semibold mb-4">Comments</h3>
              <div className="space-y-4">
                {comments.map((comment) => (
                  <div key={comment.id} className="border rounded-lg p-4">
                    <div className="flex items-start gap-3">
                      <div className="h-8 w-8 rounded-full bg-primary/10 flex items-center justify-center">
                        <User className="h-4 w-4 text-primary" />
                      </div>
                      <div className="flex-1">
                        <div className="flex items-center gap-2 mb-1">
                          <span className="font-medium text-sm">
                            {comment.user?.name || "Unknown User"}
                          </span>
                          <span className="text-xs text-muted-foreground">
                            {formatDistanceToNow(new Date(comment.created_at), { addSuffix: true })}
                          </span>
                        </div>
                        <p className="text-sm">{comment.comment}</p>
                      </div>
                    </div>
                  </div>
                ))}
                {comments.length === 0 && (
                  <div className="text-center py-8 text-muted-foreground text-sm">
                    No comments yet
                  </div>
                )}
              </div>

              <div className="mt-4 space-y-2">
                <Textarea
                  placeholder="Add a comment..."
                  value={commentText}
                  onChange={(e) => setCommentText(e.target.value)}
                  rows={3}
                />
                <Button onClick={handleAddComment} disabled={submitting || !commentText.trim()}>
                  <Send className="h-4 w-4 mr-2" />
                  {submitting ? "Sending..." : "Add Comment"}
                </Button>
              </div>
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle>Ticket Information</CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <div>
              <label className="text-sm font-medium text-muted-foreground">Status</label>
              <Select value={ticket.status} onValueChange={handleStatusChange}>
                <SelectTrigger className="mt-1">
                  <SelectValue />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="open">Open</SelectItem>
                  <SelectItem value="in_progress">In Progress</SelectItem>
                  <SelectItem value="on_hold">On Hold</SelectItem>
                  <SelectItem value="resolved">Resolved</SelectItem>
                  <SelectItem value="closed">Closed</SelectItem>
                </SelectContent>
              </Select>
            </div>

            <div>
              <label className="text-sm font-medium text-muted-foreground">Assigned To</label>
              <p className="mt-1 text-sm">
                {ticket.assignee?.name || "Unassigned"}
              </p>
            </div>

            <div>
              <label className="text-sm font-medium text-muted-foreground">Created By</label>
              <p className="mt-1 text-sm">
                {ticket.creator?.name || "Unknown"}
              </p>
            </div>

            <div>
              <label className="text-sm font-medium text-muted-foreground">Created</label>
              <p className="mt-1 text-sm flex items-center gap-1">
                <Clock className="h-3 w-3" />
                {format(new Date(ticket.created_at), "PPp")}
              </p>
            </div>

            {ticket.due_date && (
              <div>
                <label className="text-sm font-medium text-muted-foreground">Due Date</label>
                <p className="mt-1 text-sm">
                  {format(new Date(ticket.due_date), "PPp")}
                </p>
              </div>
            )}

            {ticket.tags && ticket.tags.length > 0 && (
              <div>
                <label className="text-sm font-medium text-muted-foreground">Tags</label>
                <div className="flex gap-1 flex-wrap mt-1">
                  {ticket.tags.map((tag) => (
                    <Badge key={tag} variant="outline">
                      {tag}
                    </Badge>
                  ))}
                </div>
              </div>
            )}
          </CardContent>
        </Card>
      </div>
    </div>
  )
}
