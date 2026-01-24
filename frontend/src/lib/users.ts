import api from "./api"

export interface User {
  id: number
  name: string
  email: string
  username?: string
  roles: Array<{ id: number; name: string }>
  created_at: string
  updated_at: string
}

export interface StoreUserRequest {
  name: string
  email: string
  password: string
  roles?: number[]
}

export interface UpdateUserRequest {
  name?: string
  email?: string
  password?: string
  roles?: number[]
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

export async function getUsers(page: number = 1, perPage: number = 15): Promise<PaginatedResponse<User>> {
  const response = await api.get<PaginatedResponse<User>>("/users", {
    params: { page, per_page: perPage },
  })
  return response.data
}

export async function getUser(id: number): Promise<{ data: User }> {
  const response = await api.get<{ data: User }>(`/users/${id}`)
  return response.data
}

export async function createUser(data: StoreUserRequest): Promise<{ message: string; data: User }> {
  const response = await api.post<{ message: string; data: User }>("/users", data)
  return response.data
}

export async function updateUser(id: number, data: UpdateUserRequest): Promise<{ message: string; data: User }> {
  const response = await api.put<{ message: string; data: User }>(`/users/${id}`, data)
  return response.data
}

export async function deleteUser(id: number): Promise<{ message: string }> {
  const response = await api.delete<{ message: string }>(`/users/${id}`)
  return response.data
}
