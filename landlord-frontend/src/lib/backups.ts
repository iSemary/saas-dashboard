import api from "@/lib/api"

export interface Backup {
  name: string
  size: number
  created_at: string
}

export interface BackupsResponse {
  backups: Backup[]
}

export async function getBackups(): Promise<BackupsResponse> {
  const response = await api.get<BackupsResponse>("/backups")
  return response.data
}

export async function createBackup(type: string = "both"): Promise<{ message: string }> {
  const response = await api.post<{ message: string }>("/backups", { type })
  return response.data
}

export async function downloadBackup(filename: string): Promise<Blob> {
  const response = await api.get(`/backups/download/${filename}`, { responseType: "blob" })
  return response.data
}

export async function deleteBackup(filename: string): Promise<{ message: string }> {
  const response = await api.delete<{ message: string }>(`/backups/${filename}`)
  return response.data
}
