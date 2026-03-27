"use client"

import { useState, useEffect } from "react"
import { useNotifications } from "@/context/notification-context"
import { NotificationItem } from "./NotificationItem"
import { Button } from "@/components/ui/button"
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs"
import { Input } from "@/components/ui/input"
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select"
import { Loader2, Search, CheckCircle2, Circle } from "lucide-react"
import { toast } from "sonner"

export function NotificationCenter() {
  const {
    notifications,
    loading,
    loadNotifications,
    markAllNotificationsAsRead,
    markNotificationAsRead,
  } = useNotifications()
  const [activeTab, setActiveTab] = useState<"all" | "unread" | "read">("all")
  const [search, setSearch] = useState("")
  const [typeFilter, setTypeFilter] = useState<string>("all")
  const [priorityFilter, setPriorityFilter] = useState<string>("all")

  useEffect(() => {
    const filters: any = {
      per_page: 50,
    }

    if (activeTab === "unread") {
      filters.status = "unread"
    } else if (activeTab === "read") {
      filters.status = "read"
    }

    if (typeFilter !== "all") {
      filters.type = typeFilter
    }

    if (priorityFilter !== "all") {
      filters.priority = priorityFilter
    }

    if (search) {
      filters.search = search
    }

    loadNotifications(filters)
  }, [activeTab, typeFilter, priorityFilter, search, loadNotifications])

  const filteredNotifications = notifications.filter((n) => {
    if (activeTab === "unread" && n.is_read) return false
    if (activeTab === "read" && !n.is_read) return false
    return true
  })

  const unreadNotifications = notifications.filter((n) => !n.is_read)
  const readNotifications = notifications.filter((n) => n.is_read)

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-3xl font-bold">Notifications</h1>
          <p className="text-muted-foreground">Manage your notifications</p>
        </div>
        {unreadNotifications.length > 0 && (
          <Button onClick={markAllNotificationsAsRead} variant="outline">
            <CheckCircle2 className="h-4 w-4 mr-2" />
            Mark all as read
          </Button>
        )}
      </div>

      {/* Filters */}
      <div className="grid gap-4 md:grid-cols-3">
        <div className="relative">
          <Search className="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
          <Input
            placeholder="Search notifications..."
            value={search}
            onChange={(e) => setSearch(e.target.value)}
            className="pl-8"
          />
        </div>
        <Select value={typeFilter} onValueChange={setTypeFilter}>
          <SelectTrigger>
            <SelectValue placeholder="Filter by type" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="all">All types</SelectItem>
            <SelectItem value="info">Info</SelectItem>
            <SelectItem value="alert">Alert</SelectItem>
            <SelectItem value="announcement">Announcement</SelectItem>
          </SelectContent>
        </Select>
        <Select value={priorityFilter} onValueChange={setPriorityFilter}>
          <SelectTrigger>
            <SelectValue placeholder="Filter by priority" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="all">All priorities</SelectItem>
            <SelectItem value="low">Low</SelectItem>
            <SelectItem value="medium">Medium</SelectItem>
            <SelectItem value="high">High</SelectItem>
          </SelectContent>
        </Select>
      </div>

      {/* Tabs */}
      <Tabs value={activeTab} onValueChange={(v) => setActiveTab(v as any)}>
        <TabsList>
          <TabsTrigger value="all">
            All
            {notifications.length > 0 && (
              <span className="ml-2 text-xs text-muted-foreground">
                ({notifications.length})
              </span>
            )}
          </TabsTrigger>
          <TabsTrigger value="unread">
            Unread
            {unreadNotifications.length > 0 && (
              <span className="ml-2 rounded-full bg-primary text-primary-foreground px-1.5 py-0.5 text-xs">
                {unreadNotifications.length}
              </span>
            )}
          </TabsTrigger>
          <TabsTrigger value="read">
            Read
            {readNotifications.length > 0 && (
              <span className="ml-2 text-xs text-muted-foreground">
                ({readNotifications.length})
              </span>
            )}
          </TabsTrigger>
        </TabsList>

        <TabsContent value="all" className="space-y-2">
          {loading ? (
            <div className="flex items-center justify-center py-12">
              <Loader2 className="h-8 w-8 animate-spin text-muted-foreground" />
            </div>
          ) : filteredNotifications.length > 0 ? (
            <div className="space-y-1">
              {filteredNotifications.map((notification) => (
                <NotificationItem key={notification.id} notification={notification} />
              ))}
            </div>
          ) : (
            <div className="text-center py-12 text-muted-foreground">
              No notifications found
            </div>
          )}
        </TabsContent>

        <TabsContent value="unread" className="space-y-2">
          {loading ? (
            <div className="flex items-center justify-center py-12">
              <Loader2 className="h-8 w-8 animate-spin text-muted-foreground" />
            </div>
          ) : unreadNotifications.length > 0 ? (
            <div className="space-y-1">
              {unreadNotifications.map((notification) => (
                <NotificationItem key={notification.id} notification={notification} />
              ))}
            </div>
          ) : (
            <div className="text-center py-12 text-muted-foreground">
              No unread notifications
            </div>
          )}
        </TabsContent>

        <TabsContent value="read" className="space-y-2">
          {loading ? (
            <div className="flex items-center justify-center py-12">
              <Loader2 className="h-8 w-8 animate-spin text-muted-foreground" />
            </div>
          ) : readNotifications.length > 0 ? (
            <div className="space-y-1">
              {readNotifications.map((notification) => (
                <NotificationItem key={notification.id} notification={notification} />
              ))}
            </div>
          ) : (
            <div className="text-center py-12 text-muted-foreground">
              No read notifications
            </div>
          )}
        </TabsContent>
      </Tabs>
    </div>
  )
}
