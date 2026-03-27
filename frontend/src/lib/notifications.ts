import api from "./api"

export interface Notification {
  id: number
  title: string
  body: string
  type: string
  priority?: string
  icon?: string
  route?: string
  data?: Record<string, any>
  is_read: boolean
  seen_at?: string
  created_at: string
}

export interface NotificationPreferences {
  email: boolean
  push: boolean
  in_app: boolean
  types?: Record<string, {
    email: boolean
    push: boolean
    in_app: boolean
  }>
}

export interface NotificationsResponse {
  data: {
    data: Notification[]
    current_page: number
    last_page: number
    per_page: number
    total: number
    from: number | null
    to: number | null
  }
}

export interface UnreadCountResponse {
  data: {
    count: number
  }
}

export interface NotificationStatsResponse {
  data: {
    total: number
    unread: number
    read: number
    by_type: Record<string, number>
  }
}

export interface NotificationPreferencesResponse {
  data: NotificationPreferences
}

export async function getNotifications(filters?: {
  status?: "all" | "read" | "unread"
  type?: string
  priority?: string
  search?: string
  page?: number
  per_page?: number
}): Promise<NotificationsResponse> {
  const params = filters || {}
  const response = await api.get<NotificationsResponse>("/notifications", { params })
  return response.data
}

export async function getUnreadCount(): Promise<number> {
  const response = await api.get<UnreadCountResponse>("/notifications/unread-count")
  return response.data.data.count
}

export async function getNotificationStats(): Promise<NotificationStatsResponse> {
  const response = await api.get<NotificationStatsResponse>("/notifications/stats")
  return response.data
}

export async function markAsRead(id: number): Promise<void> {
  await api.post(`/notifications/${id}/read`)
}

export async function markAsUnread(id: number): Promise<void> {
  await api.post(`/notifications/${id}/unread`)
}

export async function markAllAsRead(): Promise<void> {
  await api.post("/notifications/read-all")
}

export async function deleteNotification(id: number): Promise<void> {
  await api.delete(`/notifications/${id}`)
}

export async function getNotificationPreferences(): Promise<NotificationPreferencesResponse> {
  const response = await api.get<NotificationPreferencesResponse>("/notifications/preferences")
  return response.data
}

export async function updateNotificationPreferences(
  preferences: NotificationPreferences
): Promise<NotificationPreferencesResponse> {
  const response = await api.put<NotificationPreferencesResponse>(
    "/notifications/preferences",
    preferences
  )
  return response.data
}
