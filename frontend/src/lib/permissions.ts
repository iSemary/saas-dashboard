import api from "./api"

export interface Permission {
  id: number
  name: string
  created_at: string
  updated_at: string
}

export interface StorePermissionRequest {
  name: string
}

export interface UpdatePermissionRequest {
  name: string
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

export async function getPermissions(page: number = 1, perPage: number = 15): Promise<PaginatedResponse<Permission>> {
  const response = await api.get<PaginatedResponse<Permission>>("/permissions", {
    params: { page, per_page: perPage },
  })
  return response.data
}

export async function getPermission(id: number): Promise<{ data: Permission }> {
  const response = await api.get<{ data: Permission }>(`/permissions/${id}`)
  return response.data
}

export async function createPermission(data: StorePermissionRequest): Promise<{ message: string; data: Permission }> {
  const response = await api.post<{ message: string; data: Permission }>("/permissions", data)
  return response.data
}

export async function updatePermission(id: number, data: UpdatePermissionRequest): Promise<{ message: string; data: Permission }> {
  const response = await api.put<{ message: string; data: Permission }>(`/permissions/${id}`, data)
  return response.data
}

export async function deletePermission(id: number): Promise<{ message: string }> {
  const response = await api.delete<{ message: string }>(`/permissions/${id}`)
  return response.data
}
