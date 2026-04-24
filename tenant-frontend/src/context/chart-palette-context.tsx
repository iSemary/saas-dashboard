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
import { DEFAULT_CHART_PALETTE, parseChartPalette } from "@/lib/chart-palette";

type ChartPaletteContextValue = {
  palette: string[];
  refresh: () => Promise<void>;
};

const ChartPaletteContext = createContext<ChartPaletteContextValue | null>(null);

export function ChartPaletteProvider({ children }: { children: ReactNode }) {
  const [palette, setPalette] = useState<string[]>(() => [...DEFAULT_CHART_PALETTE]);

  const refresh = useCallback(async () => {
    try {
      const res = await api.get<{ settings: Record<string, string> }>("/settings");
      setPalette(parseChartPalette(res.data.settings.chart_palette));
    } catch {
      setPalette([...DEFAULT_CHART_PALETTE]);
    }
  }, []);

  useEffect(() => {
    // eslint-disable-next-line react-hooks/set-state-in-effect -- async settings fetch updates palette after mount
    void refresh();
  }, [refresh]);

  const value = useMemo(() => ({ palette, refresh }), [palette, refresh]);

  return <ChartPaletteContext.Provider value={value}>{children}</ChartPaletteContext.Provider>;
}

export function useChartPalette(): ChartPaletteContextValue {
  const ctx = useContext(ChartPaletteContext);
  if (!ctx) {
    return { palette: [...DEFAULT_CHART_PALETTE], refresh: async () => {} };
  }
  return ctx;
}
