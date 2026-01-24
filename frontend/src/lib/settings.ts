import api from "./api"

export interface Settings {
  [key: string]: string | null
}

export async function getSettings(): Promise<Settings> {
  const res = await api.get<{ settings: Settings }>("/settings")
  return res.data?.settings ?? {}
}

export async function updateSettings(settings: Record<string, string | null>): Promise<Settings> {
  const res = await api.put<{ message: string; settings: Settings }>("/settings", { settings })
  return res.data?.settings ?? {}
}
