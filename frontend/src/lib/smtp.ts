import api from "./api"

export interface SMTPConfig {
  id: number
  name: string
  description?: string
  from_address: string
  from_name: string
  mailer: "smtp" | "ses" | "mailgun" | "postmark"
  host: string
  port: number
  username?: string
  password?: string
  encryption?: "tls" | "ssl"
  status: "active" | "inactive"
  created_at: string
  updated_at: string
}

export interface SMTPConfigsResponse {
  data: SMTPConfig[]
}

export async function getSMTPConfigs(): Promise<SMTPConfigsResponse> {
  const response = await api.get<SMTPConfigsResponse>("/email/smtp")
  return response.data
}

export async function getSMTPConfig(id: number): Promise<{ data: SMTPConfig }> {
  const response = await api.get<{ data: SMTPConfig }>(`/email/smtp/${id}`)
  return response.data
}

export async function createSMTPConfig(data: Partial<SMTPConfig>): Promise<{ data: SMTPConfig; message: string }> {
  const response = await api.post<{ data: SMTPConfig; message: string }>("/email/smtp", data)
  return response.data
}

export async function updateSMTPConfig(
  id: number,
  data: Partial<SMTPConfig>
): Promise<{ data: SMTPConfig; message: string }> {
  const response = await api.put<{ data: SMTPConfig; message: string }>(`/email/smtp/${id}`, data)
  return response.data
}

export async function deleteSMTPConfig(id: number): Promise<{ message: string }> {
  const response = await api.delete<{ message: string }>(`/email/smtp/${id}`)
  return response.data
}

export async function testSMTPConnection(id: number, testEmail: string): Promise<{ message: string }> {
  const response = await api.post<{ message: string }>(`/email/smtp/${id}/test`, { test_email: testEmail })
  return response.data
}
