import api from "./api"

export interface AnalyticsData {
  revenue_trend?: Array<{
    date: string
    revenue: number
  }>
  user_growth?: Array<{
    date: string
    users: number
  }>
  activity_distribution?: Array<{
    type: string
    count: number
  }>
  performance_metrics?: Array<{
    metric: string
    value: number
  }>
}

export interface KPIData {
  revenue?: {
    current: number
    previous: number
    growth: number
    target?: number
  }
  users?: {
    current: number
    previous: number
    growth: number
    target?: number
  }
  customers?: {
    current: number
    previous: number
    growth: number
    target?: number
  }
  subscriptions?: {
    current: number
    previous: number
    growth: number
    target?: number
  }
}

export interface AnalyticsResponse {
  data: AnalyticsData
}

export interface KPIResponse {
  data: KPIData
}

export async function getAnalytics(period?: string): Promise<AnalyticsResponse> {
  const params = period ? { period } : {}
  const response = await api.get<AnalyticsResponse>("/dashboard/analytics", { params })
  return response.data
}

export async function getKPIs(period?: string): Promise<KPIResponse> {
  const params = period ? { period } : {}
  const response = await api.get<KPIResponse>("/dashboard/kpis", { params })
  return response.data
}
