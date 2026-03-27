"use client"

import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Badge } from "@/components/ui/badge"
import { type ActivityLog } from "@/lib/activity"
import { Clock, User, FileText, Edit, Trash2, Plus } from "lucide-react"
import { formatDistanceToNow } from "date-fns"

interface ActivityTimelineProps {
  logs: ActivityLog[]
}

const eventIcons = {
  created: Plus,
  updated: Edit,
  deleted: Trash2,
  restored: FileText,
}

const eventColors = {
  created: "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200",
  updated: "bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200",
  deleted: "bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200",
  restored: "bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200",
}

export function ActivityTimeline({ logs }: ActivityTimelineProps) {
  if (logs.length === 0) {
    return (
      <Card>
        <CardHeader>
          <CardTitle>Activity Timeline</CardTitle>
          <CardDescription>No activity logs found</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="flex items-center justify-center h-[400px] text-muted-foreground">
            No activity to display
          </div>
        </CardContent>
      </Card>
    )
  }

  // Group logs by date
  const groupedLogs = logs.reduce((acc, log) => {
    const date = new Date(log.created_at).toLocaleDateString()
    if (!acc[date]) {
      acc[date] = []
    }
    acc[date].push(log)
    return acc
  }, {} as Record<string, ActivityLog[]>)

  return (
    <Card>
      <CardHeader>
        <CardTitle>Activity Timeline</CardTitle>
        <CardDescription>Chronological view of all activities</CardDescription>
      </CardHeader>
      <CardContent>
        <div className="space-y-6">
          {Object.entries(groupedLogs).map(([date, dateLogs]) => (
            <div key={date} className="space-y-4">
              <div className="flex items-center gap-2">
                <div className="h-px flex-1 bg-border" />
                <h3 className="text-sm font-semibold text-muted-foreground">{date}</h3>
                <div className="h-px flex-1 bg-border" />
              </div>
              <div className="space-y-3 pl-4 border-l-2 border-border">
                {dateLogs.map((log) => {
                  const EventIcon = eventIcons[log.event as keyof typeof eventIcons] || FileText
                  const eventColor = eventColors[log.event as keyof typeof eventColors] || "bg-gray-100 text-gray-800"

                  return (
                    <div key={log.id} className="relative pb-4 last:pb-0">
                      <div className="absolute -left-[9px] top-1 h-4 w-4 rounded-full bg-background border-2 border-border" />
                      <div className="ml-6 space-y-2">
                        <div className="flex items-start justify-between gap-4">
                          <div className="flex-1 space-y-1">
                            <div className="flex items-center gap-2">
                              <EventIcon className="h-4 w-4 text-muted-foreground" />
                              <Badge className={eventColor} variant="outline">
                                {log.event}
                              </Badge>
                              <span className="text-sm font-medium">{log.type}</span>
                              {log.type_id && (
                                <span className="text-xs text-muted-foreground">#{log.type_id}</span>
                              )}
                            </div>
                            {log.user && (
                              <div className="flex items-center gap-1 text-xs text-muted-foreground">
                                <User className="h-3 w-3" />
                                <span>{log.user.name}</span>
                              </div>
                            )}
                            {log.url && (
                              <p className="text-xs text-muted-foreground truncate max-w-md">
                                {log.url}
                              </p>
                            )}
                          </div>
                          <div className="flex items-center gap-1 text-xs text-muted-foreground whitespace-nowrap">
                            <Clock className="h-3 w-3" />
                            <span>{formatDistanceToNow(new Date(log.created_at), { addSuffix: true })}</span>
                          </div>
                        </div>
                        {(log.old_values || log.new_values) && (
                          <div className="text-xs space-y-1 pl-6">
                            {log.old_values && Object.keys(log.old_values).length > 0 && (
                              <div>
                                <span className="font-medium text-red-600 dark:text-red-400">Old:</span>{" "}
                                {Object.entries(log.old_values)
                                  .slice(0, 3)
                                  .map(([key, value]) => `${key}: ${value}`)
                                  .join(", ")}
                                {Object.keys(log.old_values).length > 3 && "..."}
                              </div>
                            )}
                            {log.new_values && Object.keys(log.new_values).length > 0 && (
                              <div>
                                <span className="font-medium text-green-600 dark:text-green-400">New:</span>{" "}
                                {Object.entries(log.new_values)
                                  .slice(0, 3)
                                  .map(([key, value]) => `${key}: ${value}`)
                                  .join(", ")}
                                {Object.keys(log.new_values).length > 3 && "..."}
                              </div>
                            )}
                          </div>
                        )}
                      </div>
                    </div>
                  )
                })}
              </div>
            </div>
          ))}
        </div>
      </CardContent>
    </Card>
  )
}
