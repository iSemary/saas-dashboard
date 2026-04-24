/** Rough relative luminance 0–1 for #RRGGBB (sRGB). */
function hexLuminance(hex: string): number {
  const h = hex.replace(/^#/, "");
  if (h.length !== 6) return 0.5;
  const r = parseInt(h.slice(0, 2), 16) / 255;
  const g = parseInt(h.slice(2, 4), 16) / 255;
  const b = parseInt(h.slice(4, 6), 16) / 255;
  return 0.2126 * r + 0.7152 * g + 0.0722 * b;
}

function contrastForegroundHex(bgHex: string): string {
  return hexLuminance(bgHex) > 0.55 ? "#0a0a0a" : "#fafafa";
}

const THEME_VARS = [
  "--primary",
  "--primary-foreground",
  "--secondary",
  "--secondary-foreground",
  "--accent",
  "--accent-foreground",
  "--sidebar-primary",
  "--sidebar-primary-foreground",
] as const;

/** Apply user theme colors using CSS `oklch(from #hex …)` (Chrome 111+, Safari 16.4+). */
export function applyDashboardThemeColors(opts: {
  primary: string | null;
  secondary: string | null;
  accent: string | null;
}): void {
  const root = document.documentElement;

  const applyPair = (hex: string | null, baseVar: string, fgVar: string) => {
    if (!hex || !/^#[0-9A-Fa-f]{6}$/.test(hex)) {
      root.style.removeProperty(baseVar);
      root.style.removeProperty(fgVar);
      return;
    }
    const fg = contrastForegroundHex(hex);
    root.style.setProperty(baseVar, `oklch(from ${hex} l c h)`);
    root.style.setProperty(fgVar, `oklch(from ${fg} l c h)`);
  };

  applyPair(opts.primary, "--primary", "--primary-foreground");
  applyPair(opts.secondary, "--secondary", "--secondary-foreground");
  applyPair(opts.accent, "--accent", "--accent-foreground");

  if (opts.primary && /^#[0-9A-Fa-f]{6}$/.test(opts.primary)) {
    const fg = contrastForegroundHex(opts.primary);
    root.style.setProperty("--sidebar-primary", `oklch(from ${opts.primary} l c h)`);
    root.style.setProperty("--sidebar-primary-foreground", `oklch(from ${fg} l c h)`);
  } else {
    root.style.removeProperty("--sidebar-primary");
    root.style.removeProperty("--sidebar-primary-foreground");
  }
}

export function clearDashboardThemeColors(): void {
  const root = document.documentElement;
  for (const v of THEME_VARS) {
    root.style.removeProperty(v);
  }
}
