"use client"

import { useState, useEffect } from "react"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog"
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table"
import { Badge } from "@/components/ui/badge"
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs"
import {
  getWebhooks,
  createWebhook,
  updateWebhook,
  deleteWebhook,
  testWebhook,
  getWebhookLogs,
  type Webhook,
  type WebhookLog,
} from "@/lib/webhooks"
import { Plus, Edit, Trash2, Play, Eye, Loader2 } from "lucide-react"
import { toast } from "sonner"
import { useForm } from "react-hook-form"
import { zodResolver } from "@hookform/resolvers/zod"
import * as z from "zod"
import {
  Form,
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from "@/components/ui/form"
import { Textarea } from "@/components/ui/textarea"
import { Checkbox } from "@/components/ui/checkbox"
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select"
import { format } from "date-fns"

const webhookSchema = z.object({
  name: z.string().min(1, "Name is required"),
  url: z.string().url("Invalid URL"),
  secret: z.string().optional(),
  events: z.array(z.string()).optional(),
  status: z.enum(["active", "inactive"]),
  timeout: z.coerce.number().min(1).max(300),
  retry_count: z.coerce.number().min(0).max(10),
})

type WebhookFormValues = z.infer<typeof webhookSchema>

const availableEvents = [
  "customer.created",
  "customer.updated",
  "customer.deleted",
  "ticket.created",
  "ticket.updated",
  "ticket.closed",
  "payment.completed",
  "payment.failed",
  "subscription.created",
  "subscription.updated",
  "subscription.cancelled",
]

export default function WebhooksPage() {
  const [webhooks, setWebhooks] = useState<Webhook[]>([])
  const [loading, setLoading] = useState(true)
  const [dialogOpen, setDialogOpen] = useState(false)
  const [detailDialogOpen, setDetailDialogOpen] = useState(false)
  const [selectedWebhook, setSelectedWebhook] = useState<Webhook | null>(null)
  const [logs, setLogs] = useState<WebhookLog[]>([])
  const [testing, setTesting] = useState(false)

  const form = useForm<WebhookFormValues>({
    resolver: zodResolver(webhookSchema),
    defaultValues: {
      name: "",
      url: "",
      secret: "",
      events: [],
      status: "active",
      timeout: 30,
      retry_count: 3,
    },
  })

  useEffect(() => {
    loadWebhooks()
  }, [])

  useEffect(() => {
    if (selectedWebhook) {
      form.reset({
        name: selectedWebhook.name,
        url: selectedWebhook.url,
        secret: selectedWebhook.secret || "",
        events: selectedWebhook.events || [],
        status: selectedWebhook.status,
        timeout: selectedWebhook.timeout,
        retry_count: selectedWebhook.retry_count,
      })
    } else {
      form.reset({
        name: "",
        url: "",
        secret: "",
        events: [],
        status: "active",
        timeout: 30,
        retry_count: 3,
      })
    }
  }, [selectedWebhook, form])

  useEffect(() => {
    if (selectedWebhook && detailDialogOpen) {
      loadLogs()
    }
  }, [selectedWebhook, detailDialogOpen])

  const loadWebhooks = async () => {
    try {
      setLoading(true)
      const response = await getWebhooks()
      setWebhooks(response.data)
    } catch (error: any) {
      toast.error("Failed to load webhooks")
      console.error(error)
    } finally {
      setLoading(false)
    }
  }

  const loadLogs = async () => {
    if (!selectedWebhook) return
    try {
      const response = await getWebhookLogs(selectedWebhook.id)
      setLogs(response.data.data)
    } catch (error) {
      console.error("Failed to load logs", error)
    }
  }

  const handleSubmit = async (values: WebhookFormValues) => {
    try {
      if (selectedWebhook) {
        await updateWebhook(selectedWebhook.id, values)
        toast.success("Webhook updated successfully")
      } else {
        await createWebhook(values)
        toast.success("Webhook created successfully")
      }
      setDialogOpen(false)
      setSelectedWebhook(null)
      loadWebhooks()
    } catch (error: any) {
      toast.error(selectedWebhook ? "Failed to update webhook" : "Failed to create webhook")
      console.error(error)
    }
  }

  const handleDelete = async (id: number) => {
    if (!confirm("Are you sure you want to delete this webhook?")) return

    try {
      await deleteWebhook(id)
      toast.success("Webhook deleted successfully")
      loadWebhooks()
    } catch (error: any) {
      toast.error("Failed to delete webhook")
      console.error(error)
    }
  }

  const handleTest = async (id: number) => {
    try {
      setTesting(true)
      const response = await testWebhook(id, { test: true, timestamp: new Date().toISOString() })
      if (response.data.success) {
        toast.success("Test webhook sent successfully")
      } else {
        toast.error("Test webhook failed")
      }
      loadWebhooks()
    } catch (error: any) {
      toast.error("Failed to send test webhook")
      console.error(error)
    } finally {
      setTesting(false)
    }
  }

  const handleViewDetails = async (webhook: Webhook) => {
    setSelectedWebhook(webhook)
    setDetailDialogOpen(true)
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-3xl font-bold">Webhooks</h1>
          <p className="text-muted-foreground">Manage your webhook endpoints</p>
        </div>
        <Button
          onClick={() => {
            setSelectedWebhook(null)
            setDialogOpen(true)
          }}
        >
          <Plus className="h-4 w-4 mr-2" />
          New Webhook
        </Button>
      </div>

      {loading ? (
        <div className="text-center py-12 text-muted-foreground">Loading...</div>
      ) : (
        <div className="border rounded-lg">
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Name</TableHead>
                <TableHead>URL</TableHead>
                <TableHead>Events</TableHead>
                <TableHead>Status</TableHead>
                <TableHead>Actions</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {webhooks.length === 0 ? (
                <TableRow>
                  <TableCell colSpan={5} className="text-center text-muted-foreground py-8">
                    No webhooks found
                  </TableCell>
                </TableRow>
              ) : (
                webhooks.map((webhook) => (
                  <TableRow key={webhook.id}>
                    <TableCell className="font-medium">{webhook.name}</TableCell>
                    <TableCell className="max-w-md truncate">{webhook.url}</TableCell>
                    <TableCell>
                      {webhook.events && webhook.events.length > 0 ? (
                        <div className="flex gap-1 flex-wrap">
                          {webhook.events.slice(0, 2).map((event) => (
                            <Badge key={event} variant="outline" className="text-xs">
                              {event}
                            </Badge>
                          ))}
                          {webhook.events.length > 2 && (
                            <Badge variant="outline" className="text-xs">
                              +{webhook.events.length - 2}
                            </Badge>
                          )}
                        </div>
                      ) : (
                        <span className="text-muted-foreground text-sm">All events</span>
                      )}
                    </TableCell>
                    <TableCell>
                      <Badge variant={webhook.status === "active" ? "default" : "secondary"}>
                        {webhook.status}
                      </Badge>
                    </TableCell>
                    <TableCell>
                      <div className="flex items-center gap-2">
                        <Button
                          variant="ghost"
                          size="icon"
                          onClick={() => handleViewDetails(webhook)}
                        >
                          <Eye className="h-4 w-4" />
                        </Button>
                        <Button
                          variant="ghost"
                          size="icon"
                          onClick={() => handleTest(webhook.id)}
                          disabled={testing}
                        >
                          {testing ? (
                            <Loader2 className="h-4 w-4 animate-spin" />
                          ) : (
                            <Play className="h-4 w-4" />
                          )}
                        </Button>
                        <Button
                          variant="ghost"
                          size="icon"
                          onClick={() => {
                            setSelectedWebhook(webhook)
                            setDialogOpen(true)
                          }}
                        >
                          <Edit className="h-4 w-4" />
                        </Button>
                        <Button
                          variant="ghost"
                          size="icon"
                          onClick={() => handleDelete(webhook.id)}
                        >
                          <Trash2 className="h-4 w-4" />
                        </Button>
                      </div>
                    </TableCell>
                  </TableRow>
                ))
              )}
            </TableBody>
          </Table>
        </div>
      )}

      {/* Create/Edit Dialog */}
      <Dialog open={dialogOpen} onOpenChange={setDialogOpen}>
        <DialogContent className="max-w-2xl max-h-[90vh] overflow-y-auto">
          <DialogHeader>
            <DialogTitle>
              {selectedWebhook ? "Edit Webhook" : "Create Webhook"}
            </DialogTitle>
            <DialogDescription>
              {selectedWebhook
                ? "Update webhook configuration"
                : "Create a new webhook endpoint"}
            </DialogDescription>
          </DialogHeader>
          <Form {...form}>
            <form onSubmit={form.handleSubmit(handleSubmit)} className="space-y-4">
              <FormField
                control={form.control}
                name="name"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Name *</FormLabel>
                    <FormControl>
                      <Input {...field} />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                )}
              />

              <FormField
                control={form.control}
                name="url"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>URL *</FormLabel>
                    <FormControl>
                      <Input {...field} placeholder="https://example.com/webhook" />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                )}
              />

              <FormField
                control={form.control}
                name="secret"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Secret</FormLabel>
                    <FormControl>
                      <Input {...field} placeholder="Leave blank to auto-generate" />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                )}
              />

              <div className="grid grid-cols-2 gap-4">
                <FormField
                  control={form.control}
                  name="timeout"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Timeout (seconds)</FormLabel>
                      <FormControl>
                        <Input type="number" {...field} />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />
                <FormField
                  control={form.control}
                  name="retry_count"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Retry Count</FormLabel>
                      <FormControl>
                        <Input type="number" {...field} />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />
              </div>

              <FormField
                control={form.control}
                name="status"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Status</FormLabel>
                    <Select onValueChange={field.onChange} value={field.value}>
                      <FormControl>
                        <SelectTrigger>
                          <SelectValue />
                        </SelectTrigger>
                      </FormControl>
                      <SelectContent>
                        <SelectItem value="active">Active</SelectItem>
                        <SelectItem value="inactive">Inactive</SelectItem>
                      </SelectContent>
                    </Select>
                    <FormMessage />
                  </FormItem>
                )}
              />

              <FormField
                control={form.control}
                name="events"
                render={() => (
                  <FormItem>
                    <FormLabel>Events (leave empty for all events)</FormLabel>
                    <div className="grid grid-cols-2 gap-2 max-h-48 overflow-y-auto border rounded-lg p-4">
                      {availableEvents.map((event) => (
                        <FormField
                          key={event}
                          control={form.control}
                          name="events"
                          render={({ field }) => {
                            return (
                              <FormItem className="flex flex-row items-start space-x-3 space-y-0">
                                <FormControl>
                                  <Checkbox
                                    checked={field.value?.includes(event)}
                                    onCheckedChange={(checked) => {
                                      return checked
                                        ? field.onChange([...(field.value || []), event])
                                        : field.onChange(
                                            field.value?.filter((value) => value !== event)
                                          )
                                    }}
                                  />
                                </FormControl>
                                <FormLabel className="text-sm font-normal cursor-pointer">
                                  {event}
                                </FormLabel>
                              </FormItem>
                            )
                          }}
                        />
                      ))}
                    </div>
                    <FormMessage />
                  </FormItem>
                )}
              />

              <DialogFooter>
                <Button
                  type="button"
                  variant="outline"
                  onClick={() => {
                    setDialogOpen(false)
                    setSelectedWebhook(null)
                  }}
                >
                  Cancel
                </Button>
                <Button type="submit">
                  {selectedWebhook ? "Update" : "Create"}
                </Button>
              </DialogFooter>
            </form>
          </Form>
        </DialogContent>
      </Dialog>

      {/* Details Dialog */}
      <Dialog open={detailDialogOpen} onOpenChange={setDetailDialogOpen}>
        <DialogContent className="max-w-4xl max-h-[90vh] overflow-y-auto">
          <DialogHeader>
            <DialogTitle>{selectedWebhook?.name}</DialogTitle>
            <DialogDescription>Webhook details and delivery logs</DialogDescription>
          </DialogHeader>
          {selectedWebhook && (
            <Tabs defaultValue="details" className="w-full">
              <TabsList>
                <TabsTrigger value="details">Details</TabsTrigger>
                <TabsTrigger value="logs">Logs</TabsTrigger>
              </TabsList>

              <TabsContent value="details" className="space-y-4">
                <div className="grid grid-cols-2 gap-4">
                  <div>
                    <label className="text-sm font-medium text-muted-foreground">URL</label>
                    <p className="mt-1">{selectedWebhook.url}</p>
                  </div>
                  <div>
                    <label className="text-sm font-medium text-muted-foreground">Status</label>
                    <p className="mt-1">
                      <Badge variant={selectedWebhook.status === "active" ? "default" : "secondary"}>
                        {selectedWebhook.status}
                      </Badge>
                    </p>
                  </div>
                  <div>
                    <label className="text-sm font-medium text-muted-foreground">Timeout</label>
                    <p className="mt-1">{selectedWebhook.timeout}s</p>
                  </div>
                  <div>
                    <label className="text-sm font-medium text-muted-foreground">Retry Count</label>
                    <p className="mt-1">{selectedWebhook.retry_count}</p>
                  </div>
                  {selectedWebhook.events && selectedWebhook.events.length > 0 && (
                    <div className="col-span-2">
                      <label className="text-sm font-medium text-muted-foreground">Events</label>
                      <div className="flex gap-1 flex-wrap mt-1">
                        {selectedWebhook.events.map((event) => (
                          <Badge key={event} variant="outline">
                            {event}
                          </Badge>
                        ))}
                      </div>
                    </div>
                  )}
                </div>
              </TabsContent>

              <TabsContent value="logs" className="space-y-4">
                <div className="border rounded-lg">
                  <Table>
                    <TableHeader>
                      <TableRow>
                        <TableHead>Event</TableHead>
                        <TableHead>Status</TableHead>
                        <TableHead>Attempt</TableHead>
                        <TableHead>Delivered</TableHead>
                        <TableHead>Time</TableHead>
                      </TableRow>
                    </TableHeader>
                    <TableBody>
                      {logs.length === 0 ? (
                        <TableRow>
                          <TableCell colSpan={5} className="text-center text-muted-foreground py-8">
                            No logs found
                          </TableCell>
                        </TableRow>
                      ) : (
                        logs.map((log) => (
                          <TableRow key={log.id}>
                            <TableCell className="font-medium">{log.event}</TableCell>
                            <TableCell>
                              {log.status_code ? (
                                <Badge
                                  variant={
                                    log.status_code >= 200 && log.status_code < 300
                                      ? "default"
                                      : "destructive"
                                  }
                                >
                                  {log.status_code}
                                </Badge>
                              ) : log.error ? (
                                <Badge variant="destructive">Error</Badge>
                              ) : (
                                <Badge variant="secondary">Pending</Badge>
                              )}
                            </TableCell>
                            <TableCell>{log.attempt}</TableCell>
                            <TableCell>
                              {log.delivered_at
                                ? format(new Date(log.delivered_at), "MMM d, yyyy HH:mm")
                                : "-"}
                            </TableCell>
                            <TableCell>
                              {format(new Date(log.created_at), "MMM d, yyyy HH:mm")}
                            </TableCell>
                          </TableRow>
                        ))
                      )}
                    </TableBody>
                  </Table>
                </div>
              </TabsContent>
            </Tabs>
          )}
        </DialogContent>
      </Dialog>
    </div>
  )
}
