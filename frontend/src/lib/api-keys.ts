import api from "./api"

export interface ApiKey {
  id: number
  name: string
  last_used_at?: string
  created_at: string
  scopes?: string[]
  token?: string // Only present when creating
}

export async function getApiKeys(): Promise<{ data: ApiKey[] }> {
  const response = await api.get<{ data: ApiKey[] }>("/auth/api-keys")
  return response.data
}

export async function createApiKey(data: { name: string; scopes?: string[] }): Promise<{
  data: ApiKey
  message: string
}> {
  const response = await api.post<{ data: ApiKey; message: string }>("/auth/api-keys", data)
  return response.data
}

export async function revokeApiKey(id: number): Promise<{ message: string }> {
  const response = await api.delete<{ message: string }>(`/auth/api-keys/${id}`)
  return response.data
}
