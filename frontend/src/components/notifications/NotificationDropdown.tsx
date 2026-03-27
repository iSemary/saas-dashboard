"use client"

import { useNotifications } from "@/context/notification-context"
import { NotificationItem } from "./NotificationItem"
import { Button } from "@/components/ui/button"
import { ScrollArea } from "@/components/ui/scroll-area"
import { Loader2 } from "lucide-react"
import Link from "next/link"

export function NotificationDropdown() {
  const { notifications, loading, markAllNotificationsAsRead } = useNotifications()

  return (
    <div className="flex flex-col max-h-[400px]">
      <div className="flex items-center justify-between p-4 border-b">
        <h3 className="font-semibold text-sm">Notifications</h3>
        {notifications.length > 0 && (
          <Button
            variant="ghost"
            size="sm"
            onClick={markAllNotificationsAsRead}
            className="text-xs"
          >
            Mark all as read
          </Button>
        )}
      </div>
      <ScrollArea className="flex-1">
        {loading ? (
          <div className="flex items-center justify-center p-8">
            <Loader2 className="h-6 w-6 animate-spin text-muted-foreground" />
          </div>
        ) : notifications.length > 0 ? (
          <div className="divide-y">
            {notifications.map((notification) => (
              <NotificationItem key={notification.id} notification={notification} />
            ))}
          </div>
        ) : (
          <div className="p-8 text-center text-sm text-muted-foreground">
            No notifications
          </div>
        )}
      </ScrollArea>
      <div className="p-2 border-t">
        <Link href="/dashboard/notifications">
          <Button variant="ghost" className="w-full text-sm">
            View all notifications
          </Button>
        </Link>
      </div>
    </div>
  )
}
