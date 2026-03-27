import api from "./api"

export interface UserProfile {
  id: number
  name: string
  email: string
  username?: string
  phone?: string
  address?: string
  timezone?: string
  avatar?: string
  created_at: string
  updated_at: string
}

export interface Session {
  id: number
  name: string
  last_used_at?: string
  created_at: string
}

export async function getProfile(): Promise<{ data: UserProfile }> {
  const response = await api.get<{ data: UserProfile }>("/auth/profile")
  return response.data
}

export async function updateProfile(data: Partial<UserProfile>): Promise<{ data: UserProfile; message: string }> {
  const response = await api.put<{ data: UserProfile; message: string }>("/auth/profile", data)
  return response.data
}

export async function uploadAvatar(file: File): Promise<{ data: { avatar: string }; message: string }> {
  const formData = new FormData()
  formData.append("avatar", file)
  const response = await api.post<{ data: { avatar: string }; message: string }>("/auth/profile/avatar", formData, {
    headers: {
      "Content-Type": "multipart/form-data",
    },
  })
  return response.data
}

export async function removeAvatar(): Promise<{ message: string }> {
  const response = await api.delete<{ message: string }>("/auth/profile/avatar")
  return response.data
}

export async function changePassword(data: {
  current_password: string
  new_password: string
  new_password_confirmation: string
}): Promise<{ message: string }> {
  const response = await api.post<{ message: string }>("/auth/profile/password", data)
  return response.data
}

export async function getSessions(): Promise<{ data: Session[] }> {
  const response = await api.get<{ data: Session[] }>("/auth/profile/sessions")
  return response.data
}

export async function revokeSession(sessionId: number): Promise<{ message: string }> {
  const response = await api.post<{ message: string }>(`/auth/profile/sessions/${sessionId}/revoke`)
  return response.data
}
