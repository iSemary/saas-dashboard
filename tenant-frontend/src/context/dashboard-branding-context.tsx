"use client";

import {
  createContext,
  useCallback,
  useContext,
  useEffect,
  useMemo,
  useState,
  type ReactNode,
} from "react";
import api from "@/lib/api";
import { APP_LOGO_SRC } from "@/lib/app-config";
import { applyDashboardThemeColors, clearDashboardThemeColors } from "@/lib/dashboard-theme";
import { storageUrlFromPath } from "@/lib/backend-origin";

type DashboardBrandingContextValue = {
  /** Resolved logo URL for `<img>` / Next (custom storage or default asset path). */
  logoSrc: string;
  /** Absolute URL for tab icon when custom favicon uploaded. */
  faviconHref: string | null;
  /** Raw settings map (paths + hex). */
  settings: Record<string, string>;
  refresh: () => Promise<void>;
};

const DashboardBrandingContext = createContext<DashboardBrandingContextValue | null>(null);

const DEFAULT_FAVICON = "/assets/favicon.svg";

export function DashboardBrandingProvider({ children }: { children: ReactNode }) {
  const [settings, setSettings] = useState<Record<string, string>>({});

  const refresh = useCallback(async () => {
    try {
      const res = await api.get<{ settings: Record<string, string> }>("/settings");
      setSettings(res.data.settings ?? {});
    } catch {
      setSettings({});
    }
  }, []);

  useEffect(() => {
    void refresh();
  }, [refresh]);

  const logoSrc = useMemo(() => {
    const path = settings.branding_logo?.trim();
    const url = storageUrlFromPath(path);
    return url ?? APP_LOGO_SRC;
  }, [settings.branding_logo]);

  const faviconHref = useMemo(() => {
    const path = settings.branding_favicon?.trim();
    return storageUrlFromPath(path);
  }, [settings.branding_favicon]);

  useEffect(() => {
    applyDashboardThemeColors({
      primary: settings.dashboard_primary?.trim() || null,
      secondary: settings.dashboard_secondary?.trim() || null,
      accent: settings.dashboard_accent?.trim() || null,
    });
    return () => {
      clearDashboardThemeColors();
    };
  }, [settings.dashboard_primary, settings.dashboard_secondary, settings.dashboard_accent]);

  useEffect(() => {
    const href = faviconHref ?? DEFAULT_FAVICON;
    const isAbsolute = href.startsWith("http");
    const fullHref = isAbsolute || href.startsWith("/") ? href : `/${href}`;

    const link = document.querySelector<HTMLLinkElement>("link[rel='icon']") ?? (() => {
      const el = document.createElement("link");
      el.rel = "icon";
      document.head.appendChild(el);
      return el;
    })();
    link.href = fullHref;

    const shortcut = document.querySelector<HTMLLinkElement>("link[rel='shortcut icon']") ?? (() => {
      const el = document.createElement("link");
      el.rel = "shortcut icon";
      document.head.appendChild(el);
      return el;
    })();
    shortcut.href = fullHref;
  }, [faviconHref]);

  useEffect(() => {
    return () => {
      const link = document.querySelector<HTMLLinkElement>("link[rel='icon']");
      const shortcut = document.querySelector<HTMLLinkElement>("link[rel='shortcut icon']");
      if (link) link.href = DEFAULT_FAVICON;
      if (shortcut) shortcut.href = DEFAULT_FAVICON;
    };
  }, []);

  const value = useMemo<DashboardBrandingContextValue>(
    () => ({
      logoSrc,
      faviconHref,
      settings,
      refresh,
    }),
    [logoSrc, faviconHref, settings, refresh],
  );

  return (
    <DashboardBrandingContext.Provider value={value}>{children}</DashboardBrandingContext.Provider>
  );
}

export function useDashboardBranding(): DashboardBrandingContextValue {
  const ctx = useContext(DashboardBrandingContext);
  if (!ctx) {
    return {
      logoSrc: APP_LOGO_SRC,
      faviconHref: null,
      settings: {},
      refresh: async () => {},
    };
  }
  return ctx;
}
