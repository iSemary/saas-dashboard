import api from "./api"

export interface Webhook {
  id: number
  name: string
  url: string
  secret?: string
  events?: string[]
  status: "active" | "inactive"
  timeout: number
  retry_count: number
  headers?: Record<string, string>
  created_by: number
  created_at: string
  updated_at: string
  creator?: {
    id: number
    name: string
    email: string
  }
  logs?: WebhookLog[]
}

export interface WebhookLog {
  id: number
  webhook_id: number
  event: string
  payload: Record<string, any>
  status_code?: number
  response?: string
  error?: string
  attempt: number
  delivered_at?: string
  created_at: string
}

export interface WebhooksResponse {
  data: Webhook[]
}

export interface WebhookLogsResponse {
  data: {
    data: WebhookLog[]
    current_page: number
    last_page: number
    per_page: number
    total: number
  }
}

export async function getWebhooks(): Promise<WebhooksResponse> {
  const response = await api.get<WebhooksResponse>("/webhooks")
  return response.data
}

export async function getWebhook(id: number): Promise<{ data: Webhook }> {
  const response = await api.get<{ data: Webhook }>(`/webhooks/${id}`)
  return response.data
}

export async function createWebhook(data: Partial<Webhook>): Promise<{ data: Webhook; message: string }> {
  const response = await api.post<{ data: Webhook; message: string }>("/webhooks", data)
  return response.data
}

export async function updateWebhook(
  id: number,
  data: Partial<Webhook>
): Promise<{ data: Webhook; message: string }> {
  const response = await api.put<{ data: Webhook; message: string }>(`/webhooks/${id}`, data)
  return response.data
}

export async function deleteWebhook(id: number): Promise<{ message: string }> {
  const response = await api.delete<{ message: string }>(`/webhooks/${id}`)
  return response.data
}

export async function testWebhook(id: number, payload?: Record<string, any>): Promise<{
  data: { success: boolean; status_code: number; response: string }
  message: string
}> {
  const response = await api.post<{
    data: { success: boolean; status_code: number; response: string }
    message: string
  }>(`/webhooks/${id}/test`, { payload })
  return response.data
}

export async function getWebhookLogs(id: number, page: number = 1): Promise<WebhookLogsResponse> {
  const response = await api.get<WebhookLogsResponse>(`/webhooks/${id}/logs`, { params: { page } })
  return response.data
}
