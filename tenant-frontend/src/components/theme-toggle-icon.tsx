"use client";

import { Moon, Sun } from "lucide-react";
import { useTheme } from "next-themes";
import { useEffect, useState } from "react";
import { useI18n } from "@/context/i18n-context";

export function ThemeToggleIcon() {
  const { t } = useI18n();
  const { setTheme, resolvedTheme } = useTheme();
  const [mounted, setMounted] = useState(false);

  useEffect(() => {
    setMounted(true);
  }, []);

  if (!mounted) {
    return <div className="size-8 rounded-lg border border-border/80 bg-background/80" aria-hidden />;
  }

  const isDark = resolvedTheme === "dark";
  const toggle = () => setTheme(isDark ? "light" : "dark");

  return (
    <button
      type="button"
      onClick={toggle}
      className="inline-flex size-8 items-center justify-center rounded-lg border border-border/80 bg-background/80 text-foreground shadow-sm outline-none transition hover:bg-muted focus-visible:ring-2 focus-visible:ring-ring"
      aria-label={
        isDark
          ? t("dashboard.theme.switch_to_light", "Switch to light mode")
          : t("dashboard.theme.switch_to_dark", "Switch to dark mode")
      }
    >
      {isDark ? <Sun className="size-4" /> : <Moon className="size-4" />}
    </button>
  );
}
