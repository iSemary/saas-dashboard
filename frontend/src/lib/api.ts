import axios from "axios"

const api = axios.create({
  baseURL: process.env.NEXT_PUBLIC_API_BASE_URL ?? "http://customer1.saas.test/api",
})

api.interceptors.request.use((config) => {
  if (typeof window !== "undefined") {
    const token = window.localStorage.getItem("auth_token")
    if (token) {
      config.headers = {
        ...config.headers,
        Authorization: `Bearer ${token}`,
      }
    }
  }

  return config
})

api.interceptors.response.use(
  (response) => response,
  (error) => {
    // Handle 401 Unauthorized responses
    if (
      error.response?.status === 401 &&
      typeof window !== "undefined"
    ) {
      const url = error.config?.url || ""
      
      // Don't logout on login/logout endpoints to avoid infinite loops
      if (!url.includes("/auth/login") && !url.includes("/auth/logout")) {
        // Clear authentication data
        window.localStorage.removeItem("auth_token")
        window.localStorage.removeItem("auth_user")
        
        // Only redirect if we're not already on the login page
        if (!window.location.pathname.startsWith("/login")) {
          window.location.href = "/login"
        }
      }
    }
    
    return Promise.reject(error)
  }
)

export interface AuthUser {
  id: number
  name: string
  email: string
  username?: string
  two_factor_enabled?: boolean
  roles?: Array<{ id: number; name: string }>
  permissions?: string[]
}

export interface AuthResponse {
  token: string
  user: AuthUser
}

export interface TwoFactorRequiredResponse {
  requires_2fa: true
  temp_token: string
  message: string
}

export interface TwoFactorSetupResponse {
  secret: string
  qr_code_url: string
}

export interface TwoFactorConfirmResponse {
  message: string
  recovery_codes: string[]
}

export interface TwoFactorVerifyResponse {
  token: string
  user: AuthUser
}

export default api
