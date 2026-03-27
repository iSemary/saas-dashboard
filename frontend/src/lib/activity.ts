import api from "./api"

export interface ActivityLog {
  id: number
  event: string
  type: string
  type_id: number
  user?: {
    id: number
    name: string
    email: string
  }
  old_values?: Record<string, any>
  new_values?: Record<string, any>
  url?: string
  ip_address?: string
  user_agent?: string
  created_at: string
}

export interface ActivityLogsResponse {
  data: ActivityLog[]
  pagination: {
    current_page: number
    last_page: number
    per_page: number
    total: number
    from: number | null
    to: number | null
  }
}

export interface ActivityLogFilters {
  user_id?: number
  event_type?: string
  date_from?: string
  date_to?: string
  search?: string
  page?: number
  per_page?: number
}

export async function getActivityLogs(
  filters?: ActivityLogFilters
): Promise<ActivityLogsResponse> {
  const params = filters || {}
  const response = await api.get<ActivityLogsResponse>("/activity-logs", { params })
  return response.data
}

export async function getActivityLog(id: number): Promise<{ data: ActivityLog }> {
  const response = await api.get<{ data: ActivityLog }>(`/activity-logs/${id}`)
  return response.data
}

export async function exportActivityLogs(
  filters?: ActivityLogFilters,
  format: string = "csv"
): Promise<Blob> {
  const params = { ...filters, format }
  const response = await api.get("/activity-logs/export", {
    params,
    responseType: "blob",
  })
  return response.data
}
