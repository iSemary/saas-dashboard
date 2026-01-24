import api from "./api"

export interface Role {
  id: number
  name: string
  permissions: Array<{ id: number; name: string }>
  created_at: string
  updated_at: string
}

export interface StoreRoleRequest {
  name: string
  permissions?: number[]
}

export interface UpdateRoleRequest {
  name: string
  permissions?: number[]
}

export interface PaginatedResponse<T> {
  data: T[]
  current_page: number
  last_page: number
  per_page: number
  total: number
  from: number | null
  to: number | null
}

export async function getRoles(page: number = 1, perPage: number = 15): Promise<PaginatedResponse<Role>> {
  const response = await api.get<PaginatedResponse<Role>>("/roles", {
    params: { page, per_page: perPage },
  })
  return response.data
}

export async function getRole(id: number): Promise<{ data: Role }> {
  const response = await api.get<{ data: Role }>(`/roles/${id}`)
  return response.data
}

export async function createRole(data: StoreRoleRequest): Promise<{ message: string; data: Role }> {
  const response = await api.post<{ message: string; data: Role }>("/roles", data)
  return response.data
}

export async function updateRole(id: number, data: UpdateRoleRequest): Promise<{ message: string; data: Role }> {
  const response = await api.put<{ message: string; data: Role }>(`/roles/${id}`, data)
  return response.data
}

export async function deleteRole(id: number): Promise<{ message: string }> {
  const response = await api.delete<{ message: string }>(`/roles/${id}`)
  return response.data
}
