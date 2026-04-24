"use client";

import { createContext, useCallback, useContext, useEffect, useMemo, useRef, useState } from "react";
import api from "@/lib/api";

type Locale = "en" | "ar";

interface I18nContextValue {
  locale: Locale;
  dir: "ltr" | "rtl";
  /** Resolve a key from loaded namespaces (default bundle includes `dashboard`). */
  t: (key: string, fallback?: string) => string;
  setLocale: (locale: Locale) => void;
  /** Load a namespace from the API and merge into the client dictionary for the current locale. */
  ensureNamespace: (namespace: string) => Promise<void>;
}

const I18nContext = createContext<I18nContextValue | undefined>(undefined);

function bundleCacheKey(locale: string, namespace: string): string {
  return `${locale}:${namespace}`;
}

export function I18nProvider({ children }: { children: React.ReactNode }) {
  const [locale, setLocaleState] = useState<Locale>(() => {
    if (typeof window !== "undefined") {
      const saved = window.localStorage.getItem("locale");
      if (saved === "en" || saved === "ar") return saved;
    }
    return "en";
  });

  const [merged, setMerged] = useState<Record<string, string>>({});
  const loadedRef = useRef<Set<string>>(new Set());

  const loadNamespace = useCallback(async (namespace: string, force = false): Promise<void> => {
    const ck = bundleCacheKey(locale, namespace);
    if (!force && loadedRef.current.has(ck)) {
      return;
    }
    const res = await api.get<{ translations: Record<string, string> }>("/translations", {
      params: { locale, namespace },
    });
    const next = res.data.translations ?? {};
    setMerged((prev) => ({ ...prev, ...next }));
    loadedRef.current.add(ck);
  }, [locale]);

  useEffect(() => {
    document.documentElement.dir = locale === "ar" ? "rtl" : "ltr";
    document.documentElement.lang = locale;
    window.localStorage.setItem("locale", locale);
  }, [locale]);

  useEffect(() => {
    loadedRef.current = new Set();
    setMerged({});
    void loadNamespace("dashboard", true);
  }, [locale, loadNamespace]);

  const ensureNamespace = useCallback(
    async (namespace: string) => {
      await loadNamespace(namespace, false);
    },
    [loadNamespace],
  );

  const value = useMemo<I18nContextValue>(
    () => ({
      locale,
      dir: locale === "ar" ? "rtl" : "ltr",
      t: (key: string, fallback?: string) => merged[key] ?? fallback ?? key,
      setLocale: (next) => setLocaleState(next),
      ensureNamespace,
    }),
    [locale, merged, ensureNamespace],
  );

  return <I18nContext.Provider value={value}>{children}</I18nContext.Provider>;
}

export function useI18n(): I18nContextValue {
  const ctx = useContext(I18nContext);
  if (!ctx) throw new Error("useI18n must be used within I18nProvider");
  return ctx;
}
