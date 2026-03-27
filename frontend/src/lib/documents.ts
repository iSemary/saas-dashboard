import api from "./api"

export interface Document {
  id: number
  folder_id?: number
  hash_name: string
  checksum: string
  original_name: string
  mime_type: string
  host: string
  status: "active" | "inactive" | "archived"
  access_level: "private" | "public"
  size: number
  metadata?: Record<string, any>
  is_encrypted: boolean
  created_at: string
  updated_at: string
  folder?: Folder
}

export interface Folder {
  id: number
  name: string
  description?: string
  parent_id?: number
  status: "active" | "inactive"
  created_at: string
  updated_at: string
  files?: Document[]
  parent?: Folder
  children?: Folder[]
}

export interface DocumentsResponse {
  data: {
    data: Document[]
    current_page: number
    last_page: number
    per_page: number
    total: number
    from: number | null
    to: number | null
  }
}

export interface FoldersResponse {
  data: Folder[]
}

export interface DocumentFilters {
  folder_id?: number | null
  search?: string
  page?: number
  per_page?: number
}

export async function getDocuments(filters?: DocumentFilters): Promise<DocumentsResponse> {
  const params = filters || {}
  const response = await api.get<DocumentsResponse>("/documents", { params })
  return response.data
}

export async function getDocument(id: number): Promise<{ data: Document }> {
  const response = await api.get<{ data: Document }>(`/documents/${id}`)
  return response.data
}

export async function uploadDocument(
  file: File,
  folderId?: number,
  accessLevel: "private" | "public" = "public"
): Promise<{ data: Document; message: string }> {
  const formData = new FormData()
  formData.append("file", file)
  if (folderId) formData.append("folder_id", folderId.toString())
  formData.append("access_level", accessLevel)

  const response = await api.post<{ data: Document; message: string }>("/documents/upload", formData, {
    headers: {
      "Content-Type": "multipart/form-data",
    },
  })
  return response.data
}

export async function updateDocument(
  id: number,
  data: Partial<Document>
): Promise<{ data: Document; message: string }> {
  const response = await api.put<{ data: Document; message: string }>(`/documents/${id}`, data)
  return response.data
}

export async function deleteDocument(id: number): Promise<{ message: string }> {
  const response = await api.delete<{ message: string }>(`/documents/${id}`)
  return response.data
}

export async function downloadDocument(id: number): Promise<Blob> {
  const response = await api.get(`/documents/${id}/download`, {
    responseType: "blob",
  })
  return response.data
}

export async function getDocumentVersions(id: number): Promise<{ data: any[] }> {
  const response = await api.get<{ data: any[] }>(`/documents/${id}/versions`)
  return response.data
}

export async function bulkDeleteDocuments(ids: number[]): Promise<{ message: string }> {
  const response = await api.post<{ message: string }>("/documents/bulk-delete", { ids })
  return response.data
}

export async function getFolders(parentId?: number | null, search?: string): Promise<FoldersResponse> {
  const params: any = {}
  if (parentId !== undefined) params.parent_id = parentId
  if (search) params.search = search
  const response = await api.get<FoldersResponse>("/documents/folders", { params })
  return response.data
}

export async function getFolder(id: number): Promise<{ data: Folder }> {
  const response = await api.get<{ data: Folder }>(`/documents/folders/${id}`)
  return response.data
}

export async function createFolder(data: Partial<Folder>): Promise<{ data: Folder; message: string }> {
  const response = await api.post<{ data: Folder; message: string }>("/documents/folders", data)
  return response.data
}

export async function updateFolder(
  id: number,
  data: Partial<Folder>
): Promise<{ data: Folder; message: string }> {
  const response = await api.put<{ data: Folder; message: string }>(`/documents/folders/${id}`, data)
  return response.data
}

export async function deleteFolder(id: number): Promise<{ message: string }> {
  const response = await api.delete<{ message: string }>(`/documents/folders/${id}`)
  return response.data
}

export function formatFileSize(bytes: number): string {
  if (bytes === 0) return "0 Bytes"
  const k = 1024
  const sizes = ["Bytes", "KB", "MB", "GB", "TB"]
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + " " + sizes[i]
}

export function getFileIcon(mimeType: string): string {
  if (mimeType.startsWith("image/")) return "🖼️"
  if (mimeType.startsWith("video/")) return "🎥"
  if (mimeType.startsWith("audio/")) return "🎵"
  if (mimeType.includes("pdf")) return "📄"
  if (mimeType.includes("word") || mimeType.includes("document")) return "📝"
  if (mimeType.includes("excel") || mimeType.includes("spreadsheet")) return "📊"
  if (mimeType.includes("zip") || mimeType.includes("archive")) return "📦"
  return "📎"
}
