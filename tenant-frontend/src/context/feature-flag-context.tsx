"use client";

import { createContext, useCallback, useContext, useEffect, useMemo, useState } from "react";
import api from "@/lib/api";

interface FeatureFlagContextValue {
  flags: Record<string, boolean>;
  isEnabled: (key: string, defaultValue?: boolean) => boolean;
  refresh: () => Promise<void>;
}

const FeatureFlagContext = createContext<FeatureFlagContextValue | undefined>(undefined);

export function FeatureFlagProvider({ children }: { children: React.ReactNode }) {
  const [flags, setFlags] = useState<Record<string, boolean>>({});

  const refresh = useCallback(async () => {
    try {
      const res = await api.get<{ flags: Record<string, boolean> }>("/feature-flags/evaluate");
      setFlags(res.data.flags ?? {});
    } catch {
      setFlags({});
    }
  }, []);

  useEffect(() => {
    let mounted = true;
    api.get<{ flags: Record<string, boolean> }>("/feature-flags/evaluate")
      .then((res) => {
        if (mounted) setFlags(res.data.flags ?? {});
      })
      .catch(() => {
        if (mounted) setFlags({});
      });
    return () => {
      mounted = false;
    };
  }, []);

  const value = useMemo<FeatureFlagContextValue>(() => ({
    flags,
    isEnabled: (key, defaultValue = true) => (key in flags ? !!flags[key] : defaultValue),
    refresh,
  }), [flags, refresh]);

  return <FeatureFlagContext.Provider value={value}>{children}</FeatureFlagContext.Provider>;
}

export function useFeatureFlags(): FeatureFlagContextValue {
  const ctx = useContext(FeatureFlagContext);
  if (!ctx) throw new Error("useFeatureFlags must be used within FeatureFlagProvider");
  return ctx;
}
