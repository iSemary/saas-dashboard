/** Module color palette definitions. */
export type ModulePalette = {
  primary: string;
  secondary: string;
  accent: string;
  chart: string[];
};

/** Fallback palettes per module key when backend doesn't provide one. */
export const MODULE_PALETTES: Record<string, ModulePalette> = {
  crm: {
    primary: "#3b82f6",
    secondary: "#1e40af",
    accent: "#60a5fa",
    chart: ["#3b82f6", "#60a5fa", "#93c5fd", "#1e40af", "#2563eb", "#dbeafe"],
  },
  hr: {
    primary: "#8b5cf6",
    secondary: "#5b21b6",
    accent: "#a78bfa",
    chart: ["#8b5cf6", "#a78bfa", "#c4b5fd", "#5b21b6", "#7c3aed", "#ede9fe"],
  },
  pos: {
    primary: "#f97316",
    secondary: "#c2410c",
    accent: "#fb923c",
    chart: ["#f97316", "#fb923c", "#fdba74", "#c2410c", "#ea580c", "#fff7ed"],
  },
};

/** Resolve a module palette from backend data, falling back to defaults. */
export function resolveModulePalette(
  moduleKey: string,
  backendPalette: ModulePalette | null | undefined,
): ModulePalette {
  if (backendPalette?.primary && backendPalette?.chart?.length) {
    return backendPalette;
  }
  return MODULE_PALETTES[moduleKey] ?? MODULE_PALETTES.crm;
}
