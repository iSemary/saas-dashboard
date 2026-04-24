"use client";

import Image from "next/image";
import Link from "next/link";
import { APP_LOGO_SRC, APP_NAME } from "@/lib/app-config";
import { AppFooter } from "@/components/app-footer";
import { LanguageSwitcher } from "@/components/language-switcher";
import { ThemeToggleIcon } from "@/components/theme-toggle-icon";

export function LoginShell({ children }: { children: React.ReactNode }) {
  return (
    <div className="flex min-h-svh flex-col bg-muted/30">
      <header className="flex h-14 shrink-0 items-center justify-between gap-3 border-b border-border/80 bg-card/90 px-4 backdrop-blur supports-backdrop-filter:bg-card/80">
        <Link href="/" className="flex min-w-0 items-center gap-2.5">
          <Image src={APP_LOGO_SRC} alt="" width={36} height={36} className="size-9 shrink-0 rounded-xl shadow-sm" priority />
          <span className="truncate text-sm font-semibold tracking-tight sm:text-base">{APP_NAME}</span>
        </Link>
        <div className="flex shrink-0 items-center gap-2">
          <LanguageSwitcher />
          <ThemeToggleIcon />
        </div>
      </header>
      <div className="flex flex-1 flex-col">{children}</div>
      <AppFooter />
    </div>
  );
}
