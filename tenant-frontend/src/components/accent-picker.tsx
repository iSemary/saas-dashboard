"use client";

import { Check, Palette } from "lucide-react";
import { ACCENT_PRESETS, type AccentId, useAccent } from "@/context/accent-context";
import { buttonVariants } from "@/components/ui/button";
import { cn } from "@/lib/utils";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuGroup,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";

export function AccentPicker() {
  const { accent, setAccent } = useAccent();

  return (
    <DropdownMenu>
      <DropdownMenuTrigger
        className={cn(
          buttonVariants({ variant: "outline", size: "sm" }),
          "gap-1.5 border-border/80",
        )}
      >
        <Palette className="size-3.5" />
        <span className="hidden sm:inline">Accent</span>
      </DropdownMenuTrigger>
      <DropdownMenuContent align="end" className="w-44">
        <DropdownMenuGroup>
          <DropdownMenuLabel>Accent color</DropdownMenuLabel>
          <DropdownMenuSeparator />
          {ACCENT_PRESETS.map((p) => (
            <DropdownMenuItem
              key={p.id}
              onClick={() => setAccent(p.id as AccentId)}
              className="justify-between gap-2"
            >
              {p.label}
              {accent === p.id ? <Check className="size-4" /> : null}
            </DropdownMenuItem>
          ))}
        </DropdownMenuGroup>
      </DropdownMenuContent>
    </DropdownMenu>
  );
}
