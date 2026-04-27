"use client";

import { useEffect, useState } from "react";
import { useParams, useRouter } from "next/navigation";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { Textarea } from "@/components/ui/textarea";
import { useI18n } from "@/context/i18n-context";
import { toast } from "sonner";
import api from "@/lib/api";
import {
  ArrowLeft,
  Edit,
  Printer,
  UserPlus,
  Tag,
  Flag,
  XCircle,
  RotateCcw,
  Send,
  Paperclip,
  Clock,
  AlertTriangle,
} from "lucide-react";
import { formatDistanceToNow, format } from "date-fns";

interface Ticket {
  id: number;
  ticket_number: string;
  title: string;
  description: string;
  html_content?: string;
  status: string;
  priority: string;
  created_at: string;
  due_date?: string;
  resolved_at?: string;
  closed_at?: string;
  creator: {
    id: number;
    name: string;
    avatar?: string;
  };
  assignee?: {
    id: number;
    name: string;
    avatar?: string;
  };
  brand?: {
    id: number;
    name: string;
  };
  tags?: string[];
}

interface Comment {
  id: number;
  comment: string;
  is_private: boolean;
  created_at: string;
  user: {
    id: number;
    name: string;
    avatar?: string;
  };
  attachments?: Array<{
    id: number;
    filename: string;
    size: number;
  }>;
}

interface TimelineItem {
  type: string;
  title: string;
  description?: string;
  user: string;
  user_avatar?: string;
  timestamp: string;
}

interface SLAMetrics {
  time_in_current_status: number;
  status_history: Array<{
    status: string;
    duration: number;
  }>;
  is_overdue: boolean;
}

export default function TicketDetailClient() {
  const { id } = useParams();
  const router = useRouter();
  const { t } = useI18n();
  const [loading, setLoading] = useState(true);
  const [ticket, setTicket] = useState<Ticket | null>(null);
  const [comments, setComments] = useState<Comment[]>([]);
  const [timeline, setTimeline] = useState<TimelineItem[]>([]);
  const [sla, setSla] = useState<SLAMetrics | null>(null);
  const [newComment, setNewComment] = useState("");
  const [submitting, setSubmitting] = useState(false);

  const loadTicket = async () => {
    try {
      setLoading(true);
      const response = await api.get(`/tickets/${id}`);
      const data = response.data.data;
      
      setTicket(data.ticket);
      setComments(data.comments || []);
      setTimeline(data.timeline || []);
      setSla(data.sla_data);
    } catch {
      toast.error("Failed to load ticket");
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    loadTicket();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [id]);

  const handleAddComment = async () => {
    if (!newComment.trim()) return;
    
    setSubmitting(true);
    try {
      await api.post(`/tickets/${id}/comments`, {
        comment: newComment,
      });
      
      setNewComment("");
      toast.success("Comment added");
      loadTicket();
    } catch {
      toast.error("Failed to add comment");
    } finally {
      setSubmitting(false);
    }
  };

  const handleAssign = async () => {
    // TODO: Show assign dialog
    toast.info("Assign functionality coming soon");
  };

  const handleChangeStatus = async () => {
    // TODO: Show status dialog
    toast.info("Status change functionality coming soon");
  };

  const handleChangePriority = async () => {
    // TODO: Show priority dialog
    toast.info("Priority change functionality coming soon");
  };

  const handleClose = async () => {
    try {
      await api.put(`/tickets/${id}/close`);
      toast.success("Ticket closed");
      loadTicket();
    } catch {
      toast.error("Failed to close ticket");
    }
  };

  const handleReopen = async () => {
    try {
      await api.put(`/tickets/${id}/reopen`);
      toast.success("Ticket reopened");
      loadTicket();
    } catch {
      toast.error("Failed to reopen ticket");
    }
  };

  const getStatusBadge = (status: string) => {
    const variants: Record<string, string> = {
      open: "bg-blue-100 text-blue-800",
      in_progress: "bg-yellow-100 text-yellow-800",
      waiting: "bg-orange-100 text-orange-800",
      resolved: "bg-green-100 text-green-800",
      closed: "bg-gray-100 text-gray-800",
    };
    return variants[status] || "bg-gray-100";
  };

  const getPriorityBadge = (priority: string) => {
    const variants: Record<string, string> = {
      low: "bg-green-100 text-green-800",
      medium: "bg-blue-100 text-blue-800",
      high: "bg-orange-100 text-orange-800",
      urgent: "bg-red-100 text-red-800",
    };
    return variants[priority] || "bg-gray-100";
  };

  if (loading) {
    return (
      <div className="flex h-full items-center justify-center">
        <div className="text-muted-foreground">Loading...</div>
      </div>
    );
  }

  if (!ticket) {
    return (
      <div className="flex h-full items-center justify-center">
        <div className="text-muted-foreground">Ticket not found</div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-4">
          <Button variant="outline" size="sm" onClick={() => router.back()}>
            <ArrowLeft className="mr-2 size-4" />
            Back
          </Button>
          <div>
            <h1 className="text-2xl font-semibold">
              <span className="text-muted-foreground">{ticket.ticket_number}</span>
              {" - "}
              {ticket.title}
            </h1>
          </div>
        </div>
        <div className="flex items-center gap-2">
          <Button variant="outline" size="sm" onClick={() => router.push(`/dashboard/tickets/${id}/edit`)}>
            <Edit className="mr-2 size-4" />
            Edit
          </Button>
          <Button variant="outline" size="sm">
            <Printer className="mr-2 size-4" />
            Print
          </Button>
        </div>
      </div>

      {/* Status Bar */}
      <div className="flex items-center gap-4 rounded-lg border bg-muted/40 p-4">
        <div>
          <span className="text-sm text-muted-foreground">Status:</span>
          <Badge className={`ml-2 ${getStatusBadge(ticket.status)}`}>
            {ticket.status.replace("_", " ")}
          </Badge>
        </div>
        <div>
          <span className="text-sm text-muted-foreground">Priority:</span>
          <Badge className={`ml-2 ${getPriorityBadge(ticket.priority)}`}>
            {ticket.priority}
          </Badge>
          {sla?.is_overdue && (
            <Badge variant="destructive" className="ml-2">
              <AlertTriangle className="mr-1 size-3" />
              Overdue
            </Badge>
          )}
        </div>
      </div>

      <div className="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {/* Main Content */}
        <div className="space-y-6 lg:col-span-2">
          {/* Description */}
          <Card>
            <CardHeader>
              <CardTitle>Description</CardTitle>
            </CardHeader>
            <CardContent>
              {ticket.html_content ? (
                <div dangerouslySetInnerHTML={{ __html: ticket.html_content }} />
              ) : (
                <p className="whitespace-pre-wrap">{ticket.description}</p>
              )}
            </CardContent>
          </Card>

          {/* Tags */}
          {ticket.tags && ticket.tags.length > 0 && (
            <Card>
              <CardHeader>
                <CardTitle>Tags</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="flex flex-wrap gap-2">
                  {ticket.tags.map((tag) => (
                    <Badge key={tag} variant="outline">
                      {tag}
                    </Badge>
                  ))}
                </div>
              </CardContent>
            </Card>
          )}

          {/* Comments */}
          <Card>
            <CardHeader>
              <CardTitle>Comments ({comments.length})</CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              {/* Add Comment */}
              <div className="space-y-2">
                <Textarea
                  placeholder="Add a comment..."
                  value={newComment}
                  onChange={(e) => setNewComment(e.target.value)}
                  rows={4}
                />
                <div className="flex items-center justify-between">
                  <Button variant="outline" size="sm">
                    <Paperclip className="mr-2 size-4" />
                    Attach Files
                  </Button>
                  <Button
                    size="sm"
                    disabled={!newComment.trim() || submitting}
                    onClick={handleAddComment}
                  >
                    <Send className="mr-2 size-4" />
                    {submitting ? "Sending..." : "Add Comment"}
                  </Button>
                </div>
              </div>

              {/* Comments List */}
              <div className="space-y-4">
                {comments.map((comment) => (
                  <div key={comment.id} className="border-l-2 border-primary/20 pl-4">
                    <div className="flex items-start gap-3">
                      <Avatar className="size-8">
                        <AvatarImage src={comment.user.avatar} />
                        <AvatarFallback>{comment.user.name[0]}</AvatarFallback>
                      </Avatar>
                      <div className="flex-1">
                        <div className="flex items-center gap-2">
                          <span className="font-medium">{comment.user.name}</span>
                          <span className="text-xs text-muted-foreground">
                            {formatDistanceToNow(new Date(comment.created_at), { addSuffix: true })}
                          </span>
                          {comment.is_private && (
                            <Badge variant="secondary" className="text-xs">Private</Badge>
                          )}
                        </div>
                        <p className="mt-1 whitespace-pre-wrap">{comment.comment}</p>
                        {comment.attachments && comment.attachments.length > 0 && (
                          <div className="mt-2 flex flex-wrap gap-2">
                            {comment.attachments.map((att) => (
                              <Button key={att.id} variant="outline" size="sm">
                                <Paperclip className="mr-2 size-3" />
                                {att.filename}
                              </Button>
                            ))}
                          </div>
                        )}
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>
        </div>

        {/* Sidebar */}
        <div className="space-y-6">
          {/* Assignee */}
          <Card>
            <CardHeader>
              <CardTitle>Assignee</CardTitle>
            </CardHeader>
            <CardContent>
              {ticket.assignee ? (
                <div className="flex items-center gap-3">
                  <Avatar>
                    <AvatarImage src={ticket.assignee.avatar} />
                    <AvatarFallback>{ticket.assignee.name[0]}</AvatarFallback>
                  </Avatar>
                  <div>
                    <p className="font-medium">{ticket.assignee.name}</p>
                  </div>
                </div>
              ) : (
                <p className="text-muted-foreground">Unassigned</p>
              )}
            </CardContent>
          </Card>

          {/* Metadata */}
          <Card>
            <CardHeader>
              <CardTitle>Details</CardTitle>
            </CardHeader>
            <CardContent className="space-y-3">
              <div>
                <span className="text-sm text-muted-foreground">Created by:</span>
                <div className="flex items-center gap-2 mt-1">
                  <Avatar className="size-6">
                    <AvatarImage src={ticket.creator.avatar} />
                    <AvatarFallback>{ticket.creator.name[0]}</AvatarFallback>
                  </Avatar>
                  <span>{ticket.creator.name}</span>
                </div>
              </div>
              <div>
                <span className="text-sm text-muted-foreground">Created:</span>
                <p>{format(new Date(ticket.created_at), "MMM d, yyyy HH:mm")}</p>
              </div>
              {ticket.due_date && (
                <div>
                  <span className="text-sm text-muted-foreground">Due date:</span>
                  <p>{format(new Date(ticket.due_date), "MMM d, yyyy HH:mm")}</p>
                </div>
              )}
              {ticket.brand && (
                <div>
                  <span className="text-sm text-muted-foreground">Brand:</span>
                  <p>{ticket.brand.name}</p>
                </div>
              )}
            </CardContent>
          </Card>

          {/* SLA */}
          {sla && (
            <Card>
              <CardHeader>
                <CardTitle>SLA Information</CardTitle>
              </CardHeader>
              <CardContent className="space-y-3">
                <div className="flex items-center gap-2">
                  <Clock className="size-4 text-primary" />
                  <span>Time in current status:</span>
                  <span className="font-medium">
                    {Math.floor(sla.time_in_current_status / 3600)}h
                  </span>
                </div>
                {sla.is_overdue && (
                  <div className="flex items-center gap-2 text-red-600">
                    <AlertTriangle className="size-4" />
                    <span>Overdue</span>
                  </div>
                )}
              </CardContent>
            </Card>
          )}

          {/* Actions */}
          <Card>
            <CardHeader>
              <CardTitle>Actions</CardTitle>
            </CardHeader>
            <CardContent className="space-y-2">
              <Button variant="outline" className="w-full justify-start" onClick={handleAssign}>
                <UserPlus className="mr-2 size-4" />
                Assign
              </Button>
              <Button variant="outline" className="w-full justify-start" onClick={handleChangeStatus}>
                <Tag className="mr-2 size-4" />
                Change Status
              </Button>
              <Button variant="outline" className="w-full justify-start" onClick={handleChangePriority}>
                <Flag className="mr-2 size-4" />
                Change Priority
              </Button>
              {ticket.status !== "closed" ? (
                <Button variant="destructive" className="w-full justify-start" onClick={handleClose}>
                  <XCircle className="mr-2 size-4" />
                  Close Ticket
                </Button>
              ) : (
                <Button variant="outline" className="w-full justify-start" onClick={handleReopen}>
                  <RotateCcw className="mr-2 size-4" />
                  Reopen Ticket
                </Button>
              )}
            </CardContent>
          </Card>

          {/* Activity Timeline */}
          <Card>
            <CardHeader>
              <CardTitle>Activity</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="space-y-4">
                {timeline.map((item, idx) => (
                  <div key={idx} className="flex gap-3">
                    <div className="relative">
                      <div className="absolute left-2 top-2 h-full w-px bg-border" />
                      <div className="relative z-10 flex size-4 items-center justify-center rounded-full bg-primary" />
                    </div>
                    <div className="flex-1 pb-4">
                      <p className="font-medium">{item.title}</p>
                      {item.description && (
                        <p className="text-sm text-muted-foreground">{item.description}</p>
                      )}
                      <p className="text-xs text-muted-foreground">
                        {item.user} • {formatDistanceToNow(new Date(item.timestamp), { addSuffix: true })}
                      </p>
                    </div>
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>
        </div>
      </div>
    </div>
  );
}
