import api from "./api"

export interface Ticket {
  id: number
  ticket_number: string
  title: string
  description: string
  html_content?: string
  status: "open" | "in_progress" | "on_hold" | "resolved" | "closed"
  priority: "low" | "medium" | "high" | "urgent"
  created_by: number
  assigned_to?: number
  brand_id?: number
  tags?: string[]
  due_date?: string
  resolved_at?: string
  closed_at?: string
  sla_data?: Record<string, any>
  metadata?: Record<string, any>
  created_at: string
  updated_at: string
  creator?: {
    id: number
    name: string
    email: string
  }
  assignee?: {
    id: number
    name: string
    email: string
  }
  comments?: Comment[]
}

export interface Comment {
  id: number
  parent_id?: number
  comment: string
  user_id: number
  seen: boolean
  object_id: number
  object_model: string
  metadata?: Record<string, any>
  created_at: string
  updated_at: string
  user?: {
    id: number
    name: string
    email: string
  }
  replies?: Comment[]
  attachments?: CommentAttachment[]
}

export interface CommentAttachment {
  id: number
  comment_id: number
  file_id: number
  created_at: string
  updated_at: string
}

export interface TicketsResponse {
  data: {
    data: Ticket[]
    current_page: number
    last_page: number
    per_page: number
    total: number
  }
}

export interface TicketFilters {
  status?: string
  priority?: string
  assigned_to?: number
  created_by?: number
  search?: string
  page?: number
  per_page?: number
}

export async function getTickets(filters?: TicketFilters): Promise<TicketsResponse> {
  const params = filters || {}
  const response = await api.get<TicketsResponse>("/v1/tickets", { params })
  return response.data
}

export async function getTicket(id: number): Promise<{ data: Ticket }> {
  const response = await api.get<{ data: Ticket }>(`/v1/tickets/${id}`)
  return response.data
}

export async function createTicket(data: Partial<Ticket>): Promise<{ data: Ticket; message: string }> {
  const response = await api.post<{ data: Ticket; message: string }>("/v1/tickets", data)
  return response.data
}

export async function updateTicket(id: number, data: Partial<Ticket>): Promise<{ data: Ticket; message: string }> {
  const response = await api.put<{ data: Ticket; message: string }>(`/v1/tickets/${id}`, data)
  return response.data
}

export async function deleteTicket(id: number): Promise<{ message: string }> {
  const response = await api.delete<{ message: string }>(`/v1/tickets/${id}`)
  return response.data
}

export async function updateTicketStatus(id: number, status: string): Promise<{ data: Ticket; message: string }> {
  const response = await api.patch<{ data: Ticket; message: string }>(`/v1/tickets/${id}/status`, { status })
  return response.data
}

export async function assignTicket(id: number, userId: number): Promise<{ data: Ticket; message: string }> {
  const response = await api.patch<{ data: Ticket; message: string }>(`/v1/tickets/${id}/assign`, { assigned_to: userId })
  return response.data
}

export async function closeTicket(id: number): Promise<{ data: Ticket; message: string }> {
  const response = await api.patch<{ data: Ticket; message: string }>(`/v1/tickets/${id}/close`)
  return response.data
}

export async function reopenTicket(id: number): Promise<{ data: Ticket; message: string }> {
  const response = await api.patch<{ data: Ticket; message: string }>(`/v1/tickets/${id}/reopen`)
  return response.data
}

export async function getTicketComments(ticketId: number): Promise<{ data: Comment[] }> {
  const response = await api.get<{ data: Comment[] }>(`/v1/comments/object/${ticketId}/Modules\\Ticket\\Entities\\Ticket`)
  return response.data
}

export async function createComment(data: {
  comment: string
  object_id: number
  object_model: string
  parent_id?: number
}): Promise<{ data: Comment; message: string }> {
  const response = await api.post<{ data: Comment; message: string }>("/v1/comments", data)
  return response.data
}

export async function updateComment(id: number, comment: string): Promise<{ data: Comment; message: string }> {
  const response = await api.put<{ data: Comment; message: string }>(`/v1/comments/${id}`, { comment })
  return response.data
}

export async function deleteComment(id: number): Promise<{ message: string }> {
  const response = await api.delete<{ message: string }>(`/v1/comments/${id}`)
  return response.data
}
