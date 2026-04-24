"use client";

import { createContext, useCallback, useContext, useEffect, useMemo, useState } from "react";

export const ACCENT_PRESETS = [
  { id: "neutral", label: "Neutral" },
  { id: "blue", label: "Blue" },
  { id: "violet", label: "Violet" },
  { id: "emerald", label: "Emerald" },
  { id: "rose", label: "Rose" },
  { id: "amber", label: "Amber" },
] as const;

export type AccentId = (typeof ACCENT_PRESETS)[number]["id"];

interface AccentContextValue {
  accent: AccentId;
  setAccent: (id: AccentId) => void;
}

const AccentContext = createContext<AccentContextValue | undefined>(undefined);

const STORAGE_KEY = "accent_preset";

export function AccentProvider({ children }: { children: React.ReactNode }) {
  const [accent, setAccentState] = useState<AccentId>("neutral");

  useEffect(() => {
    const saved = window.localStorage.getItem(STORAGE_KEY);
    if (saved && ACCENT_PRESETS.some((p) => p.id === saved)) {
      setAccentState(saved as AccentId);
    }
  }, []);

  useEffect(() => {
    if (accent === "neutral") {
      document.documentElement.removeAttribute("data-accent");
    } else {
      document.documentElement.setAttribute("data-accent", accent);
    }
    window.localStorage.setItem(STORAGE_KEY, accent);
  }, [accent]);

  const setAccent = useCallback((id: AccentId) => {
    setAccentState(id);
  }, []);

  const value = useMemo<AccentContextValue>(() => ({ accent, setAccent }), [accent, setAccent]);

  return <AccentContext.Provider value={value}>{children}</AccentContext.Provider>;
}

export function useAccent(): AccentContextValue {
  const ctx = useContext(AccentContext);
  if (!ctx) throw new Error("useAccent must be used within AccentProvider");
  return ctx;
}
