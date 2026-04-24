"use client";

import {
  createContext,
  useCallback,
  useContext,
  useMemo,
  useState,
  type ReactNode,
} from "react";
import { applyDashboardThemeColors, clearDashboardThemeColors } from "@/lib/dashboard-theme";
import { type ModulePalette, resolveModulePalette } from "@/lib/module-palette";

export type ActiveModule = {
  moduleKey: string;
  name: string;
  palette: ModulePalette;
} | null;

type ModuleContextValue = {
  activeModule: ActiveModule;
  setModule: (mod: ActiveModule) => void;
  clearModule: () => void;
};

const ModuleContext = createContext<ModuleContextValue | null>(null);

export function ModuleProvider({ children }: { children: ReactNode }) {
  const [activeModule, setActiveModule] = useState<ActiveModule>(null);

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

  const value = useMemo(() => ({ activeModule, setModule, clearModule }), [activeModule, setModule, clearModule]);

  return <ModuleContext.Provider value={value}>{children}</ModuleContext.Provider>;
}

export function useModule(): ModuleContextValue {
  const ctx = useContext(ModuleContext);
  if (!ctx) {
    return { activeModule: null, setModule: () => {}, clearModule: () => {} };
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
