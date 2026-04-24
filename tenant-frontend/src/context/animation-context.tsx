"use client";

import { createContext, useCallback, useContext, useEffect, useMemo, useState } from "react";
import api from "@/lib/api";

interface AnimationContextValue {
  enabled: boolean;
  loading: boolean;
  toggle: () => Promise<void>;
  setEnabled: (enabled: boolean) => Promise<void>;
}

// Helper hook for components that need animation props
export function useAnimated<T extends Record<string, unknown>>(
  animatedProps: T,
  staticProps: Partial<T> = {}
): T {
  const { enabled } = useAnimation();
  return enabled ? animatedProps : ({ ...animatedProps, ...staticProps } as T);
}

const AnimationContext = createContext<AnimationContextValue | undefined>(undefined);

const META_KEY = "animations_enabled";

export function AnimationProvider({ children }: { children: React.ReactNode }) {
  const [enabled, setEnabledState] = useState<boolean>(true);
  const [loading, setLoading] = useState(true);

  // Load animation preference on mount
  useEffect(() => {
    const loadPreference = async () => {
      try {
        // Check localStorage first for immediate UI response
        const stored = window.localStorage.getItem(META_KEY);
        if (stored !== null) {
          setEnabledState(stored === "true");
        }

        // Then fetch from API
        const res = await api.get<string | null>(`/user-meta/${META_KEY}`);
        const value = res.data;
        
        if (value !== null && value !== undefined) {
          const isEnabled = value === "true" || value === "1";
          setEnabledState(isEnabled);
          window.localStorage.setItem(META_KEY, String(isEnabled));
        } else {
          // Default to true if not set
          setEnabledState(true);
          window.localStorage.setItem(META_KEY, "true");
        }
      } catch {
        // On error, default to enabled
        setEnabledState(true);
      } finally {
        setLoading(false);
      }
    };

    loadPreference();
  }, []);

  const setEnabled = useCallback(async (value: boolean) => {
    setEnabledState(value);
    window.localStorage.setItem(META_KEY, String(value));
    
    try {
      await api.post("/user-meta", {
        key: META_KEY,
        value: value ? "true" : "false",
      });
    } catch (err) {
      console.error("Failed to save animation preference:", err);
    }
  }, []);

  const toggle = useCallback(async () => {
    await setEnabled(!enabled);
  }, [enabled, setEnabled]);

  const value = useMemo<AnimationContextValue>(
    () => ({
      enabled,
      loading,
      toggle,
      setEnabled,
    }),
    [enabled, loading, toggle, setEnabled]
  );

  return (
    <AnimationContext.Provider value={value}>
      {children}
    </AnimationContext.Provider>
  );
}

export function useAnimation(): AnimationContextValue {
  const ctx = useContext(AnimationContext);
  if (!ctx) {
    throw new Error("useAnimation must be used within AnimationProvider");
  }
  return ctx;
}
