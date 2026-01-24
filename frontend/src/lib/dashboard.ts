import api from "./api"

export interface DashboardStats {
  users?: {
    total: number
  }
  customers?: {
    total: number
  }
  subscriptions?: {
    total: number
    active: number
  }
  activity_logs?: {
    total: number
  }
}

export interface ActivityDataPoint {
  date: string
  activities: number
}

export interface RecentActivity {
  id: number
  description: string
  type: string
  user?: {
    id: number
    name: string
  }
  created_at: string
}

export interface DashboardStatsResponse {
  data: {
    stats: DashboardStats
    activity_over_time?: ActivityDataPoint[]
    recent_activity?: RecentActivity[]
  }
}

export async function getDashboardStats(): Promise<DashboardStatsResponse> {
  const response = await api.get<DashboardStatsResponse>("/dashboard/stats")
  return response.data
}
