"use client";

import { useRouter } from "next/navigation";
import { ChevronDown, Lock, LogOut, Settings, User } from "lucide-react";
import { useI18n } from "@/context/i18n-context";
import { buttonVariants } from "@/components/ui/button";
import { cn } from "@/lib/utils";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuGroup,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";

interface UserNavMenuProps {
  displayName: string | undefined;
  onLogout: () => void;
  /** Full-width trigger for sidebar footer. */
  variant?: "header" | "sidebar";
}

export function UserNavMenu({ displayName, onLogout, variant = "header" }: UserNavMenuProps) {
  const { t, dir } = useI18n();
  const router = useRouter();
  const firstName = displayName?.trim().split(/\s+/)[0] ?? t("dashboard.user_menu.account", "Account");
  const isSidebar = variant === "sidebar";

  return (
    <DropdownMenu>
      <DropdownMenuTrigger
        className={cn(
          buttonVariants({ variant: "outline", size: "sm" }),
          "gap-1.5 px-2.5",
          isSidebar
            ? "h-9 w-full max-w-none min-w-0 justify-between border-sidebar-border bg-sidebar-accent/40 text-sidebar-foreground hover:bg-sidebar-accent group-data-[collapsible=icon]:justify-center group-data-[collapsible=icon]:px-0"
            : "max-w-[220px] shrink-0 border-border/80 bg-background/80 hover:bg-accent",
        )}
      >
        <User
          className={cn(
            "size-4 shrink-0",
            isSidebar ? "hidden group-data-[collapsible=icon]:block" : "hidden",
          )}
          aria-hidden
        />
        <span className={cn("truncate", isSidebar && "group-data-[collapsible=icon]:sr-only")}>{firstName}</span>
        <ChevronDown
          className={cn("size-3.5 shrink-0 opacity-70", isSidebar && "group-data-[collapsible=icon]:hidden")}
          aria-hidden
        />
      </DropdownMenuTrigger>
      <DropdownMenuContent align={isSidebar ? "start" : dir === "rtl" ? "start" : "end"} className="min-w-48">
        <DropdownMenuGroup>
          <DropdownMenuItem
            onClick={() => {
              router.push("/dashboard/profile");
            }}
          >
            <Settings className="size-4" />
            {t("dashboard.nav.account_settings", "Account Settings")}
          </DropdownMenuItem>
          {!isSidebar ? (
            <>
              <DropdownMenuItem variant="destructive" onClick={onLogout}>
                <LogOut className="size-4" />
                {t("dashboard.actions.logout", "Logout")}
              </DropdownMenuItem>
              <DropdownMenuItem
                onClick={() => {
                  router.push("/lock-screen");
                }}
              >
                <Lock className="size-4" />
                {t("dashboard.actions.lock", "Lock")}
              </DropdownMenuItem>
            </>
          ) : null}
        </DropdownMenuGroup>
      </DropdownMenuContent>
    </DropdownMenu>
  );
}
