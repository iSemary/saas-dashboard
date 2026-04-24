/** Default dashboard chart colors (max 6). */
export const DEFAULT_CHART_PALETTE = [
  "#3b82f6",
  "#22c55e",
  "#a855f7",
  "#f97316",
  "#0ea5e9",
  "#64748b",
] as const;

const HEX = /^#([0-9A-Fa-f]{6}|[0-9A-Fa-f]{3})$/;

function normalizeHex(c: string): string | null {
  const s = c.trim();
  if (!HEX.test(s)) return null;
  if (s.length === 4) {
    const r = s[1]!;
    const g = s[2]!;
    const b = s[3]!;
    return `#${r}${r}${g}${g}${b}${b}`.toLowerCase();
  }
  return s.toLowerCase();
}

/** Parse stored JSON; invalid or empty falls back to defaults. */
export function parseChartPalette(raw: string | undefined): string[] {
  if (!raw?.trim()) return [...DEFAULT_CHART_PALETTE];
  try {
    const parsed = JSON.parse(raw) as unknown;
    if (!Array.isArray(parsed)) return [...DEFAULT_CHART_PALETTE];
    const colors: string[] = [];
    for (const item of parsed) {
      if (typeof item !== "string") continue;
      const n = normalizeHex(item);
      if (n) colors.push(n);
      if (colors.length >= 6) break;
    }
    if (colors.length === 0) return [...DEFAULT_CHART_PALETTE];
    while (colors.length < 6) {
      colors.push(DEFAULT_CHART_PALETTE[colors.length % DEFAULT_CHART_PALETTE.length]!);
    }
    return colors.slice(0, 6);
  } catch {
    return [...DEFAULT_CHART_PALETTE];
  }
}

export function serializeChartPalette(colors: string[]): string {
  const normalized: string[] = [];
  for (let i = 0; i < 6; i++) {
    const n = normalizeHex(colors[i] ?? "");
    normalized.push(n ?? DEFAULT_CHART_PALETTE[i]!);
  }
  return JSON.stringify(normalized);
}
