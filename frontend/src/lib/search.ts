import api from "./api"

export interface SearchResult {
  id: number
  type: string
  title: string
  description?: string
  url: string
}

export interface SearchResponse {
  data: {
    customers?: SearchResult[]
    tickets?: SearchResult[]
    documents?: SearchResult[]
  }
}

export async function globalSearch(
  query: string,
  types?: string[]
): Promise<SearchResponse> {
  const params: any = { q: query }
  if (types && types.length > 0) {
    params.types = types
  }
  const response = await api.get<SearchResponse>("/auth/search", { params })
  return response.data
}
