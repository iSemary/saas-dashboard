'use client'

import { createContext, useContext, useEffect, useState } from "react"
import { useRouter } from "next/navigation"
import api, {
  type AuthResponse,
  type AuthUser,
  type TwoFactorRequiredResponse,
} from "@/lib/api"
import { twoFactor } from "@/lib/two-factor"
import { toast } from "sonner"

interface AuthContextValue {
  user: AuthUser | null
  token: string | null
  isAuthenticated: boolean
  loading: boolean
  login: (data: { email: string; password: string; remember_me?: boolean }) => Promise<void>
  logout: () => Promise<void>
  verifyTwoFactor: (tempToken: string, code: string) => Promise<void>
}

const AuthContext = createContext<AuthContextValue | undefined>(undefined)

export function AuthProvider({ children }: { children: React.ReactNode }) {
  const [user, setUser] = useState<AuthUser | null>(null)
  const [token, setToken] = useState<string | null>(null)
  const [loading, setLoading] = useState(true)
  const router = useRouter()

  useEffect(() => {
    if (typeof window === "undefined") return

    const storedToken = window.localStorage.getItem("auth_token")
    const storedUser = window.localStorage.getItem("auth_user")

    if (storedToken && storedUser) {
      setToken(storedToken)
      try {
        const user = JSON.parse(storedUser) as AuthUser
        setUser(user)
        // Fetch fresh user data with permissions
        api.get<{ user: AuthUser }>("/auth/me")
          .then((response) => {
            setUser(response.data.user)
            if (typeof window !== "undefined") {
              window.localStorage.setItem("auth_user", JSON.stringify(response.data.user))
            }
          })
          .catch(() => {
            // If fetch fails, use stored user
          })
          .finally(() => {
            setLoading(false)
          })
        return
      } catch {
        setUser(null)
      }
    }

    setLoading(false)
  }, [])

  const handleAuthSuccess = (data: AuthResponse) => {
    setToken(data.token)
    setUser(data.user)

    if (typeof window !== "undefined") {
      window.localStorage.setItem("auth_token", data.token)
      window.localStorage.setItem("auth_user", JSON.stringify(data.user))
    }
  }

  const login: AuthContextValue["login"] = async (payload) => {
    try {
      setLoading(true)
      const { remember_me, ...loginPayload } = payload
      
      const response = await api.post<AuthResponse | TwoFactorRequiredResponse>(
        "/auth/login",
        loginPayload
      )

      // Handle remember me - store email if checked, remove if unchecked
      if (typeof window !== "undefined") {
        if (remember_me) {
          window.localStorage.setItem("remembered_email", payload.email)
        } else {
          window.localStorage.removeItem("remembered_email")
        }
      }

      // Check if 2FA is required
      if ("requires_2fa" in response.data && response.data.requires_2fa) {
        // Store temp token and redirect to 2FA verification
        if (typeof window !== "undefined") {
          window.localStorage.setItem("temp_token", response.data.temp_token)
        }
        router.push("/login/verify-2fa")
        return
      }

      // Normal login flow
      handleAuthSuccess(response.data as AuthResponse)
      toast.success("Logged in")
      router.push("/dashboard")
    } catch (error) {
      if (
        typeof error === "object" &&
        error !== null &&
        "response" in error &&
        (error as { response?: { status?: number } }).response?.status === 401
      ) {
        toast.error("Invalid credentials")
      } else {
        toast.error("Failed to login")
      }
      throw error
    } finally {
      setLoading(false)
    }
  }

  const verifyTwoFactor: AuthContextValue["verifyTwoFactor"] = async (
    tempToken: string,
    code: string
  ) => {
    try {
      setLoading(true)
      const response = await twoFactor.verify(tempToken, code)
      handleAuthSuccess(response)
      toast.success("Logged in")
      // Clear temp token
      if (typeof window !== "undefined") {
        window.localStorage.removeItem("temp_token")
      }
      router.push("/dashboard")
    } catch (error) {
      if (
        typeof error === "object" &&
        error !== null &&
        "response" in error &&
        (error as { response?: { status?: number } }).response?.status === 422
      ) {
        toast.error("Invalid verification code")
      } else {
        toast.error("Failed to verify code")
      }
      throw error
    } finally {
      setLoading(false)
    }
  }

  const logout: AuthContextValue["logout"] = async () => {
    try {
      if (token) {
        await api.post("/auth/logout")
      }
    } catch {
    } finally {
      setToken(null)
      setUser(null)
      if (typeof window !== "undefined") {
        window.localStorage.removeItem("auth_token")
        window.localStorage.removeItem("auth_user")
      }
      toast.success("Logged out")
      router.push("/login")
    }
  }

  const value: AuthContextValue = {
    user,
    token,
    isAuthenticated: !!user && !!token,
    loading,
    login,
    logout,
    verifyTwoFactor,
  }

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>
}

export function useAuth(): AuthContextValue {
  const ctx = useContext(AuthContext)
  if (!ctx) {
    throw new Error("useAuth must be used within AuthProvider")
  }
  return ctx
}
