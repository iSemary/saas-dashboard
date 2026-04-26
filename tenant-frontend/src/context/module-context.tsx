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
import { useAuth } from "@/context/auth-context";
import { applyDashboardThemeColors, clearDashboardThemeColors } from "@/lib/dashboard-theme";
import { type ModulePalette, resolveModulePalette } from "@/lib/module-palette";
import { getSubscribedModules } from "@/lib/tenant-resources";

export type ModuleNavItem = {
  key: string;
  label: string;
  route: string;
  icon: string;
  section?: string;
};

export type SubscribedModule = {
  id: number;
  module_id: number;
  module_key: string;
  name: string;
  description: string;
  icon: string | null;
  route: string | null;
  slogan: string | null;
  navigation: ModuleNavItem[] | null;
  status: string;
  brand_id: number;
  brand_name: string | null;
  brand_slug: string | null;
  color_palette: ModulePalette | null;
  subscribed_at: string | null;
  unread_notifications: number;
  open_tickets: number;
};

export type ActiveModule = {
  moduleKey: string;
  name: string;
  palette: ModulePalette;
} | null;

type ModuleContextValue = {
  activeModule: ActiveModule;
  setModule: (mod: ActiveModule) => void;
  clearModule: () => void;
  subscribedModules: SubscribedModule[];
  modulesLoading: boolean;
  getModuleByKey: (key: string) => SubscribedModule | undefined;
};

const ModuleContext = createContext<ModuleContextValue | null>(null);

export function ModuleProvider({ children }: { children: ReactNode }) {
  const { isAuthenticated } = useAuth();
  const [activeModule, setActiveModule] = useState<ActiveModule>(null);
  const [subscribedModules, setSubscribedModules] = useState<SubscribedModule[]>([]);
  const [modulesLoading, setModulesLoading] = useState(true);

  useEffect(() => {
    let isMounted = true;

    if (!isAuthenticated) {
      setSubscribedModules([]);
      setModulesLoading(false);
      return;
    }

    setModulesLoading(true);
    getSubscribedModules()
      .then((data) => {
        if (isMounted && Array.isArray(data)) {
          setSubscribedModules(data as SubscribedModule[]);
        }
      })
      .catch(() => {
        if (isMounted) setSubscribedModules([]);
      })
      .finally(() => {
        if (isMounted) setModulesLoading(false);
      });

    return () => {
      isMounted = false;
    };
  }, [isAuthenticated]);

  const getModuleByKey = useCallback(
    (key: string) => subscribedModules.find((m) => m.module_key === key),
    [subscribedModules],
  );

  const setModule = useCallback((mod: ActiveModule) => {
    setActiveModule(mod);
    if (mod) {
      applyDashboardThemeColors({
        primary: mod.palette.primary,
        secondary: mod.palette.secondary,
        accent: mod.palette.accent,
      });
    }
  }, []);

  const clearModule = useCallback(() => {
    setActiveModule(null);
    clearDashboardThemeColors();
  }, []);

  const value = useMemo(
    () => ({ activeModule, setModule, clearModule, subscribedModules, modulesLoading, getModuleByKey }),
    [activeModule, setModule, clearModule, subscribedModules, modulesLoading, getModuleByKey],
  );

  return <ModuleContext.Provider value={value}>{children}</ModuleContext.Provider>;
}

export function useModule(): ModuleContextValue {
  const ctx = useContext(ModuleContext);
  if (!ctx) {
    return { activeModule: null, setModule: () => {}, clearModule: () => {}, subscribedModules: [], modulesLoading: false, getModuleByKey: () => undefined };
  }
  return ctx;
}

/** Helper to build an ActiveModule from backend data. */
export function buildActiveModule(
  moduleKey: string,
  name: string,
  backendPalette: ModulePalette | null | undefined,
): ActiveModule {
  return {
    moduleKey,
    name,
    palette: resolveModulePalette(moduleKey, backendPalette),
  };
}
