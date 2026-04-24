"use client";

import { useCallback, useEffect, useMemo, useRef, useState } from "react";
import { useRouter } from "next/navigation";
import type { LucideIcon } from "lucide-react";
import { Search, X } from "lucide-react";
import { Dialog } from "@base-ui/react/dialog";
import { cn } from "@/lib/utils";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { useI18n } from "@/context/i18n-context";

export type CommandPaletteItem = {
  href: string;
  label: string;
  icon: LucideIcon;
};

type DashboardCommandPaletteProps = {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  items: CommandPaletteItem[];
};

export function DashboardCommandPalette({ open, onOpenChange, items }: DashboardCommandPaletteProps) {
  const { t } = useI18n();
  const router = useRouter();
  const [query, setQuery] = useState("");
  const [active, setActive] = useState(0);
  const inputRef = useRef<HTMLInputElement>(null);

  const filtered = useMemo(() => {
    const q = query.trim().toLowerCase();
    if (!q) return items;
    return items.filter((item) => item.label.toLowerCase().includes(q));
  }, [items, query]);

  useEffect(() => {
    setActive(0);
  }, [query, filtered.length]);

  useEffect(() => {
    if (open) {
      setQuery("");
      setActive(0);
      const t = window.setTimeout(() => inputRef.current?.focus(), 0);
      return () => window.clearTimeout(t);
    }
  }, [open]);

  const go = useCallback(
    (href: string) => {
      onOpenChange(false);
      router.push(href);
    },
    [onOpenChange, router],
  );

  useEffect(() => {
    if (!open) return;
    const onKey = (e: KeyboardEvent) => {
      if (e.key === "ArrowDown") {
        e.preventDefault();
        setActive((i) => Math.min(i + 1, Math.max(0, filtered.length - 1)));
      } else if (e.key === "ArrowUp") {
        e.preventDefault();
        setActive((i) => Math.max(i - 1, 0));
      } else if (e.key === "Enter" && filtered[active]) {
        e.preventDefault();
        go(filtered[active].href);
      }
    };
    window.addEventListener("keydown", onKey);
    return () => window.removeEventListener("keydown", onKey);
  }, [open, filtered, active, go]);

  return (
    <Dialog.Root open={open} onOpenChange={onOpenChange}>
      <Dialog.Portal>
        <Dialog.Backdrop
          className="fixed inset-0 z-50 bg-black/50 backdrop-blur-[2px] transition-opacity data-ending-style:opacity-0 data-starting-style:opacity-0"
        />
        <Dialog.Popup
          className={cn(
            "fixed top-[15%] left-1/2 z-50 flex max-h-[min(560px,75vh)] w-[min(100%-2rem,42rem)] -translate-x-1/2 flex-col overflow-hidden rounded-xl border border-border bg-popover text-popover-foreground shadow-lg",
            "transition-[opacity,transform] duration-200 data-ending-style:scale-95 data-ending-style:opacity-0 data-starting-style:scale-95 data-starting-style:opacity-0",
          )}
        >
          <Dialog.Title className="sr-only">{t("dashboard.command_palette.title_sr", "Search navigation")}</Dialog.Title>
          <div className="flex items-center gap-2 border-b border-border px-3 py-2">
            <Search className="size-4 shrink-0 text-muted-foreground" aria-hidden />
            <Input
              ref={inputRef}
              value={query}
              onChange={(e) => setQuery(e.target.value)}
              placeholder={t("dashboard.command_palette.placeholder", "Type a command or search…")}
              className="h-10 flex-1 border-0 bg-transparent px-0 shadow-none focus-visible:ring-0"
              autoComplete="off"
            />
            <Dialog.Close
              render={
                <Button
                  type="button"
                  variant="ghost"
                  size="icon-sm"
                  className="shrink-0"
                  aria-label={t("dashboard.command_palette.close", "Close")}
                />
              }
            >
              <X className="size-4" />
            </Dialog.Close>
          </div>
          <div className="min-h-0 flex-1 overflow-y-auto p-2">
            <p className="px-2 pb-1 text-xs font-medium text-muted-foreground">
              {t("dashboard.command_palette.section_go_to", "Go to")}
            </p>
            <ul className="space-y-0.5" role="listbox">
              {filtered.length === 0 ? (
                <li className="px-3 py-6 text-center text-sm text-muted-foreground">
                  {t("dashboard.command_palette.no_matches", "No matches.")}
                </li>
              ) : (
                filtered.map((item, idx) => {
                  const Icon = item.icon;
                  const selected = idx === active;
                  return (
                    <li key={item.href}>
                      <button
                        type="button"
                        role="option"
                        aria-selected={selected}
                        className={cn(
                          "flex w-full items-center gap-2 rounded-md px-2 py-2 text-start text-sm",
                          selected ? "bg-accent text-accent-foreground" : "hover:bg-muted/80",
                        )}
                        onMouseEnter={() => setActive(idx)}
                        onClick={() => go(item.href)}
                      >
                        <Icon className="size-4 shrink-0 opacity-80" />
                        <span>{item.label}</span>
                      </button>
                    </li>
                  );
                })
              )}
            </ul>
          </div>
        </Dialog.Popup>
      </Dialog.Portal>
    </Dialog.Root>
  );
}

/** Toggles the palette (e.g. bind to Ctrl+K / ⌘K). */
export function useCommandPaletteShortcut(onToggle: () => void) {
  useEffect(() => {
    const onDown = (e: KeyboardEvent) => {
      if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === "k") {
        e.preventDefault();
        onToggle();
      }
    };
    window.addEventListener("keydown", onDown);
    return () => window.removeEventListener("keydown", onDown);
  }, [onToggle]);
}
