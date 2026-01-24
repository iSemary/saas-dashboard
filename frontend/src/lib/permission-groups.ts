import api from "./api"

export interface PermissionGroup {
  id: number
  name: string
  guard_name: string
  description?: string
  permissions?: Array<{ id: number; name: string }>
  created_at: string
  updated_at: string
}

export interface StorePermissionGroupRequest {
  name: string
  guard_name?: string
  description?: string
  permissions?: number[]
}

export interface UpdatePermissionGroupRequest {
  name: string
  guard_name?: string
  description?: string
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

export async function getPermissionGroups(): Promise<{ data: PermissionGroup[] }> {
  const response = await api.get<{ data: PermissionGroup[] }>("/permission-groups")
  return response.data
}

export async function getPermissionGroup(id: number): Promise<{ data: PermissionGroup }> {
  const response = await api.get<{ data: PermissionGroup }>(`/permission-groups/${id}`)
  return response.data
}

export async function createPermissionGroup(data: StorePermissionGroupRequest): Promise<{ message: string; data: PermissionGroup }> {
  const response = await api.post<{ message: string; data: PermissionGroup }>("/permission-groups", data)
  return response.data
}

export async function updatePermissionGroup(id: number, data: UpdatePermissionGroupRequest): Promise<{ message: string; data: PermissionGroup }> {
  const response = await api.put<{ message: string; data: PermissionGroup }>(`/permission-groups/${id}`, data)
  return response.data
}

export async function deletePermissionGroup(id: number): Promise<{ message: string }> {
  const response = await api.delete<{ message: string }>(`/permission-groups/${id}`)
  return response.data
}
