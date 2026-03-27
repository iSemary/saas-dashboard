"use client"

import { formatDistanceToNow } from "date-fns"
import { useNotifications } from "@/context/notification-context"
import { type Notification } from "@/lib/notifications"
import { cn } from "@/lib/utils"
import Link from "next/link"

interface NotificationItemProps {
  notification: Notification
}

export function NotificationItem({ notification }: NotificationItemProps) {
  const { markNotificationAsRead } = useNotifications()

  const handleClick = () => {
    if (!notification.is_read) {
      markNotificationAsRead(notification.id)
    }
  }

  const content = (
    <div
      className={cn(
        "p-3 hover:bg-muted/50 transition-colors cursor-pointer",
        !notification.is_read && "bg-muted/30"
      )}
      onClick={handleClick}
    >
      <div className="flex items-start gap-3">
        <div className="flex-1 min-w-0">
          <p
            className={cn(
              "text-sm font-medium truncate",
              !notification.is_read && "font-semibold"
            )}
          >
            {notification.title}
          </p>
          <p className="text-xs text-muted-foreground line-clamp-2 mt-1">
            {notification.body}
          </p>
          <p className="text-xs text-muted-foreground mt-1">
            {formatDistanceToNow(new Date(notification.created_at), { addSuffix: true })}
          </p>
        </div>
        {!notification.is_read && (
          <div className="h-2 w-2 rounded-full bg-primary mt-2 flex-shrink-0" />
        )}
      </div>
    </div>
  )

  if (notification.route) {
    return (
      <Link href={notification.route} className="block">
        {content}
      </Link>
    )
  }

  return content
}
