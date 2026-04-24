"use client";

import { Moon, Monitor, Sun } from "lucide-react";
import { useTheme } from "next-themes";
import { useEffect, useState } from "react";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";

export function ThemeToggle() {
  const { theme, setTheme } = useTheme();
  const [mounted, setMounted] = useState(false);

  useEffect(() => {
    setMounted(true);
  }, []);

  if (!mounted) {
    return (
      <div className="h-8 w-[110px] rounded-lg border border-input bg-background/80" aria-hidden />
    );
  }

  const value = theme ?? "system";

  return (
    <Select
      value={value}
      onValueChange={(v) => {
        if (v) setTheme(v);
      }}
    >
      <SelectTrigger size="sm" className="w-[110px] border-border/80 bg-background/80" aria-label="Theme">
        <SelectValue>
          {value === "light" ? (
            <span className="flex items-center gap-1.5">
              <Sun className="size-3.5" />
              Light
            </span>
          ) : value === "dark" ? (
            <span className="flex items-center gap-1.5">
              <Moon className="size-3.5" />
              Dark
            </span>
          ) : (
            <span className="flex items-center gap-1.5">
              <Monitor className="size-3.5" />
              System
            </span>
          )}
        </SelectValue>
      </SelectTrigger>
      <SelectContent>
        <SelectItem value="light">Light</SelectItem>
        <SelectItem value="dark">Dark</SelectItem>
        <SelectItem value="system">System</SelectItem>
      </SelectContent>
    </Select>
  );
}
