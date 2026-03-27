import api from "./api"

export interface DashboardStats {
  users?: {
    total: number
    active?: number
    growth?: number
  }
  customers?: {
    total: number
    active?: number
    growth?: number
  }
  subscriptions?: {
    total: number
    active: number
    growth?: number
  }
  activity_logs?: {
    total: number
  }
  revenue?: {
    current: number
    previous: number
    growth: number
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
    analytics?: {
      revenue_trend?: Array<{ date: string; revenue: number }>
      user_growth?: Array<{ date: string; users: number }>
      activity_distribution?: Array<{ type: string; count: number }>
      performance_metrics?: Array<{ metric: string; value: number }>
    }
    kpis?: {
      revenue?: { current: number; previous: number; growth: number }
      users?: { current: number; previous: number; growth: number }
      customers?: { current: number; previous: number; growth: number }
      subscriptions?: { current: number; previous: number; growth: number }
    }
  }
}

export async function getDashboardStats(): Promise<DashboardStatsResponse> {
  const response = await api.get<DashboardStatsResponse>("/dashboard/stats")
  return response.data
}
