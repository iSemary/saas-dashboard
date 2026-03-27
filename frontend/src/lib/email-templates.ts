import api from "./api"

export interface EmailTemplate {
  id: number
  name: string
  description?: string
  subject: string
  body: string
  status: "active" | "inactive"
  variables?: string[]
  created_at: string
  updated_at: string
}

export interface EmailTemplatesResponse {
  data: {
    data: EmailTemplate[]
    current_page: number
    last_page: number
    per_page: number
    total: number
  }
}

export async function getEmailTemplates(search?: string, page: number = 1): Promise<EmailTemplatesResponse> {
  const params: any = { page }
  if (search) params.search = search
  const response = await api.get<EmailTemplatesResponse>("/email/templates", { params })
  return response.data
}

export async function getEmailTemplate(id: number): Promise<{ data: EmailTemplate }> {
  const response = await api.get<{ data: EmailTemplate }>(`/email/templates/${id}`)
  return response.data
}

export async function createEmailTemplate(data: Partial<EmailTemplate>): Promise<{ data: EmailTemplate; message: string }> {
  const response = await api.post<{ data: EmailTemplate; message: string }>("/email/templates", data)
  return response.data
}

export async function updateEmailTemplate(
  id: number,
  data: Partial<EmailTemplate>
): Promise<{ data: EmailTemplate; message: string }> {
  const response = await api.put<{ data: EmailTemplate; message: string }>(`/email/templates/${id}`, data)
  return response.data
}

export async function deleteEmailTemplate(id: number): Promise<{ message: string }> {
  const response = await api.delete<{ message: string }>(`/email/templates/${id}`)
  return response.data
}

export async function sendTestEmail(id: number, email: string): Promise<{ message: string }> {
  const response = await api.post<{ message: string }>(`/email/templates/${id}/test`, { email })
  return response.data
}
