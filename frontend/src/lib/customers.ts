import api from "./api"

export interface Company {
  id: number
  name: string
  email?: string
  phone?: string
  website?: string
  industry?: string
  employee_count?: number
  annual_revenue?: number
  address?: string
  city?: string
  state?: string
  postal_code?: string
  country?: string
  description?: string
  notes?: string
  type: "customer" | "prospect" | "partner" | "vendor" | "competitor"
  assigned_to?: number
  created_by: number
  custom_fields?: Record<string, any>
  created_at: string
  updated_at: string
  assigned_user?: {
    id: number
    name: string
    email: string
  }
  creator?: {
    id: number
    name: string
    email: string
  }
  contacts?: Contact[]
}

export interface Contact {
  id: number
  first_name: string
  last_name: string
  email?: string
  phone?: string
  mobile?: string
  title?: string
  company_id?: number
  address?: string
  city?: string
  state?: string
  postal_code?: string
  country?: string
  birthday?: string
  notes?: string
  type: "individual" | "company"
  assigned_to?: number
  created_by: number
  custom_fields?: Record<string, any>
  created_at: string
  updated_at: string
  company?: Company
  assigned_user?: {
    id: number
    name: string
    email: string
  }
  creator?: {
    id: number
    name: string
    email: string
  }
  full_name?: string
}

export interface CompaniesResponse {
  data: {
    data: Company[]
    current_page: number
    last_page: number
    per_page: number
    total: number
    from: number | null
    to: number | null
  }
}

export interface ContactsResponse {
  data: {
    data: Contact[]
    current_page: number
    last_page: number
    per_page: number
    total: number
    from: number | null
    to: number | null
  }
}

export interface CompanyFilters {
  type?: string
  industry?: string
  search?: string
  assigned_to?: number
  page?: number
  per_page?: number
}

export interface ContactFilters {
  company_id?: number
  assigned_to?: number
  search?: string
  page?: number
  per_page?: number
}

export async function getCompanies(filters?: CompanyFilters): Promise<CompaniesResponse> {
  const params = filters || {}
  const response = await api.get<CompaniesResponse>("/crm/companies", { params })
  return response.data
}

export async function getCompany(id: number): Promise<{ data: Company }> {
  const response = await api.get<{ data: Company }>(`/crm/companies/${id}`)
  return response.data
}

export async function createCompany(data: Partial<Company>): Promise<{ data: Company; message: string }> {
  const response = await api.post<{ data: Company; message: string }>("/crm/companies", data)
  return response.data
}

export async function updateCompany(id: number, data: Partial<Company>): Promise<{ data: Company; message: string }> {
  const response = await api.put<{ data: Company; message: string }>(`/crm/companies/${id}`, data)
  return response.data
}

export async function deleteCompany(id: number): Promise<{ message: string }> {
  const response = await api.delete<{ message: string }>(`/crm/companies/${id}`)
  return response.data
}

export async function bulkDeleteCompanies(ids: number[]): Promise<{ message: string }> {
  const response = await api.post<{ message: string }>("/crm/companies/bulk-delete", { ids })
  return response.data
}

export async function getCompanyActivity(id: number, page: number = 1): Promise<any> {
  const response = await api.get(`/crm/companies/${id}/activity`, { params: { page } })
  return response.data
}

export async function getContacts(filters?: ContactFilters): Promise<ContactsResponse> {
  const params = filters || {}
  const response = await api.get<ContactsResponse>("/crm/contacts", { params })
  return response.data
}

export async function getContact(id: number): Promise<{ data: Contact }> {
  const response = await api.get<{ data: Contact }>(`/crm/contacts/${id}`)
  return response.data
}

export async function createContact(data: Partial<Contact>): Promise<{ data: Contact; message: string }> {
  const response = await api.post<{ data: Contact; message: string }>("/crm/contacts", data)
  return response.data
}

export async function updateContact(id: number, data: Partial<Contact>): Promise<{ data: Contact; message: string }> {
  const response = await api.put<{ data: Contact; message: string }>(`/crm/contacts/${id}`, data)
  return response.data
}

export async function deleteContact(id: number): Promise<{ message: string }> {
  const response = await api.delete<{ message: string }>(`/crm/contacts/${id}`)
  return response.data
}

export async function bulkDeleteContacts(ids: number[]): Promise<{ message: string }> {
  const response = await api.post<{ message: string }>("/crm/contacts/bulk-delete", { ids })
  return response.data
}

export async function getContactActivity(id: number, page: number = 1): Promise<any> {
  const response = await api.get(`/crm/contacts/${id}/activity`, { params: { page } })
  return response.data
}
