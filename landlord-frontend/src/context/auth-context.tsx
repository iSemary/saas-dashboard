"use client";

import { createContext, useCallback, useContext, useEffect, useMemo, useState } from "react";
import { useRouter } from "next/navigation";
import api, { AuthUser } from "@/lib/api";
import { setAuthRedirectToLogin } from "@/lib/auth-navigation";
import { LANDLORD_SUBDOMAIN } from "@/lib/app-config";

type LoginPayload = { email: string; password: string; subdomain?: string };

interface AuthContextValue {
  user: AuthUser | null;
  token: string | null;
  loading: boolean;
  isAuthenticated: boolean;
  login: (payload: LoginPayload) => Promise<{ requires2fa?: boolean; tempToken?: string }>;
  verifyTwoFactor: (tempToken: string, code: string) => Promise<void>;
  logout: () => Promise<void>;
  refreshUser: () => Promise<void>;
}

const AuthContext = createContext<AuthContextValue | undefined>(undefined);

export function AuthProvider({ children }: { children: React.ReactNode }) {
  const [user, setUser] = useState<AuthUser | null>(null);
  const [token, setToken] = useState<string | null>(null);
  const [loading, setLoading] = useState(true);
  const router = useRouter();

  useEffect(() => {
    setAuthRedirectToLogin(() => {
      router.replace("/login");
    });
    return () => setAuthRedirectToLogin(null);
  }, [router]);

  useEffect(() => {
    const storedToken = window.localStorage.getItem("auth_token");
    const storedUser = window.localStorage.getItem("auth_user");
    if (!storedToken || !storedUser) {
      setLoading(false);
      return;
    }
    setToken(storedToken);
    setUser(JSON.parse(storedUser) as AuthUser);
    api
      .get<{ user: AuthUser }>("/auth/me")
      .then((res) => {
        setUser(res.data.user);
        window.localStorage.setItem("auth_user", JSON.stringify(res.data.user));
      })
      .catch(() => {
        setToken(null);
        setUser(null);
      })
      .finally(() => setLoading(false));
  }, []);

  const login = useCallback(async (payload: LoginPayload) => {
    const res = await api.post("/auth/login", { ...payload, subdomain: payload.subdomain ?? LANDLORD_SUBDOMAIN });
    if (res.data.requires_2fa) {
      return { requires2fa: true, tempToken: res.data.temp_token as string };
    }
    window.localStorage.setItem("auth_token", res.data.token as string);
    window.localStorage.setItem("auth_user", JSON.stringify(res.data.user as AuthUser));
    setToken(res.data.token as string);
    setUser(res.data.user as AuthUser);
    return {};
  }, []);

  const verifyTwoFactor = useCallback(async (tempToken: string, code: string) => {
    const res = await api.post("/auth/2fa/verify", { temp_token: tempToken, code, subdomain: LANDLORD_SUBDOMAIN });
    window.localStorage.setItem("auth_token", res.data.token as string);
    window.localStorage.setItem("auth_user", JSON.stringify(res.data.user as AuthUser));
    setToken(res.data.token as string);
    setUser(res.data.user as AuthUser);
  }, []);

  const logout = useCallback(async () => {
    try {
      await api.post("/auth/logout");
    } finally {
      setToken(null);
      setUser(null);
      window.localStorage.removeItem("auth_token");
      window.localStorage.removeItem("auth_user");
      router.replace("/login");
    }
  }, [router]);

  const refreshUser = useCallback(async () => {
    const res = await api.get<{ user: AuthUser }>("/auth/me");
    setUser(res.data.user);
    window.localStorage.setItem("auth_user", JSON.stringify(res.data.user));
  }, []);

  const value = useMemo<AuthContextValue>(() => ({
    user,
    token,
    loading,
    isAuthenticated: !!user && !!token,
    login,
    verifyTwoFactor,
    logout,
    refreshUser,
  }), [user, token, loading, logout, login, verifyTwoFactor, refreshUser]);

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
}

export function useAuth(): AuthContextValue {
  const ctx = useContext(AuthContext);
  if (!ctx) {
    throw new Error("useAuth must be used within AuthProvider");
  }
  return ctx;
}
