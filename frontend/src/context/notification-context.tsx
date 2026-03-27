"use client"

import { createContext, useContext, useState, useEffect, ReactNode } from "react"
import {
  getNotifications,
  getUnreadCount,
  markAsRead,
  markAllAsRead,
  type Notification,
} from "@/lib/notifications"
import { toast } from "sonner"

interface NotificationContextValue {
  notifications: Notification[]
  unreadCount: number
  loading: boolean
  loadNotifications: (filters?: any) => Promise<void>
  markNotificationAsRead: (id: number) => Promise<void>
  markAllNotificationsAsRead: () => Promise<void>
  refreshUnreadCount: () => Promise<void>
}

const NotificationContext = createContext<NotificationContextValue | undefined>(undefined)

export function NotificationProvider({ children }: { children: ReactNode }) {
  const [notifications, setNotifications] = useState<Notification[]>([])
  const [unreadCount, setUnreadCount] = useState(0)
  const [loading, setLoading] = useState(true)

  const loadNotifications = async (filters?: any) => {
    try {
      setLoading(true)
      const response = await getNotifications(filters)
      setNotifications(response.data.data)
    } catch (error: any) {
      toast.error("Failed to load notifications")
      console.error(error)
    } finally {
      setLoading(false)
    }
  }

  const refreshUnreadCount = async () => {
    try {
      const count = await getUnreadCount()
      setUnreadCount(count)
    } catch (error) {
      console.error("Failed to refresh unread count", error)
    }
  }

  const markNotificationAsRead = async (id: number) => {
    try {
      await markAsRead(id)
      setNotifications((prev) =>
        prev.map((n) => (n.id === id ? { ...n, is_read: true } : n))
      )
      await refreshUnreadCount()
    } catch (error: any) {
      toast.error("Failed to mark notification as read")
      console.error(error)
    }
  }

  const markAllNotificationsAsRead = async () => {
    try {
      await markAllAsRead()
      setNotifications((prev) => prev.map((n) => ({ ...n, is_read: true })))
      await refreshUnreadCount()
      toast.success("All notifications marked as read")
    } catch (error: any) {
      toast.error("Failed to mark all as read")
      console.error(error)
    }
  }

  useEffect(() => {
    loadNotifications({ status: "all", per_page: 10 })
    refreshUnreadCount()

    // Refresh unread count every 30 seconds
    const interval = setInterval(refreshUnreadCount, 30000)

    return () => clearInterval(interval)
  }, [])

  return (
    <NotificationContext.Provider
      value={{
        notifications,
        unreadCount,
        loading,
        loadNotifications,
        markNotificationAsRead,
        markAllNotificationsAsRead,
        refreshUnreadCount,
      }}
    >
      {children}
    </NotificationContext.Provider>
  )
}

export function useNotifications() {
  const context = useContext(NotificationContext)
  if (context === undefined) {
    throw new Error("useNotifications must be used within a NotificationProvider")
  }
  return context
}
