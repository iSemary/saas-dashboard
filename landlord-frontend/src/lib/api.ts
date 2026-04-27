import axios, { type AxiosResponse, type InternalAxiosRequestConfig } from "axios";
import { redirectToLoginSPA } from "@/lib/auth-navigation";

// Track if we're currently handling an error that should NOT trigger login redirect
let skipLoginRedirect = false;

/** Tell the API interceptor to skip the next 401 redirect (used by error boundaries) */
export function skipNextLoginRedirect(): void {
  skipLoginRedirect = true;
}

/** Check and consume the skip flag */
function shouldSkipLoginRedirect(): boolean {
  const shouldSkip = skipLoginRedirect;
  skipLoginRedirect = false;
  return shouldSkip;
}

const api = axios.create({
  baseURL: process.env.NEXT_PUBLIC_API_BASE_URL ?? "/api/landlord",
});

/** Axios response after Laravel `ApiResponse` unwrap (optional metadata from envelope). */
export type ApiAxiosResponse<T = unknown> = AxiosResponse<T> & {
  apiMessage?: string;
  apiMeta?: Record<string, unknown>;
};

function unwrapApiEnvelope(response: AxiosResponse): void {
  const payload = response.data;
  if (
    !payload ||
    typeof payload !== "object" ||
    Array.isArray(payload) ||
    !("data" in payload)
  ) {
    return;
  }
  // Accept both backend envelope shapes:
  //   A) ApiResponseEnvelope trait: { status: "success", data, message }
  //   B) Legacy ApiController::return(): { status: 200, success: true, data, message, errors, ... }
  const isEnvelopeA = (payload as { status?: unknown }).status === "success";
  const isEnvelopeB = (payload as { success?: unknown }).success === true;
  if (!isEnvelopeA && !isEnvelopeB) return;

  const p = payload as { data: unknown; message: string; meta: unknown };
  response.data = p.data as typeof response.data;
  const r = response as ApiAxiosResponse;
  r.apiMessage = p.message;
  r.apiMeta =
    p.meta && typeof p.meta === "object" && !Array.isArray(p.meta) && Object.keys(p.meta as object).length > 0
      ? (p.meta as Record<string, unknown>)
      : undefined;
}

/** Match login / 2FA / password flows — skip global 401 logout (axios `url` / `method` vary). */
function isCredentialFlowRequest(config: InternalAxiosRequestConfig | undefined): boolean {
  if (!config) return false;
  const path = String(config.url ?? "");
  const base = String(config.baseURL ?? "");
  const joined = `${base.replace(/\/$/, "")}/${path.replace(/^\//, "")}`.replace(/([^:]\/)\/+/g, "$1");
  const haystack = `${path} ${joined} ${base}`;
  return (
    haystack.includes("auth/login") ||
    haystack.includes("auth/2fa/verify") ||
    haystack.includes("auth/forgot-password") ||
    haystack.includes("auth/reset-password")
  );
}

function bearerFromConfig(config: InternalAxiosRequestConfig | undefined): string | null {
  if (!config?.headers) return null;
  const auth = config.headers.Authorization;
  if (typeof auth === "string" && auth.startsWith("Bearer ")) {
    return auth.slice(7);
  }
  if (auth && typeof auth === "object" && "get" in auth && typeof auth.get === "function") {
    const v = (auth as { get: (k: string) => string | undefined }).get("Authorization");
    if (typeof v === "string" && v.startsWith("Bearer ")) return v.slice(7);
  }
  return null;
}

api.interceptors.request.use((config) => {
  if (typeof window !== "undefined") {
    const token = window.localStorage.getItem("auth_token");
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
  }
  return config;
});

api.interceptors.response.use(
  (response) => {
    unwrapApiEnvelope(response);
    return response;
  },
  (error) => {
    if (error.response?.status === 401 && typeof window !== "undefined") {
      if (isCredentialFlowRequest(error.config)) {
        return Promise.reject(error);
      }
      // Skip redirect if we're explicitly handling an error (error boundary active)
      if (shouldSkipLoginRedirect()) {
        return Promise.reject(error);
      }
      const sentToken = bearerFromConfig(error.config);
      const currentToken = window.localStorage.getItem("auth_token");
      if (sentToken != null && currentToken != null && sentToken !== currentToken) {
        return Promise.reject(error);
      }
      window.localStorage.removeItem("auth_token");
      window.localStorage.removeItem("auth_user");
      if (!window.location.pathname.startsWith("/login")) {
        redirectToLoginSPA();
      }
    }
    return Promise.reject(error);
  },
);

export interface AuthUser {
  id: number;
  name: string;
  email: string;
  two_factor_enabled?: boolean;
  roles?: Array<{ id: number; name: string }>;
  permissions?: string[];
}

export default api;
