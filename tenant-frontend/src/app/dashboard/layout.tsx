"use client";

import Link from "next/link";
import { usePathname, useRouter } from "next/navigation";
import { useCallback, useEffect, useMemo, useState } from "react";
import type { LucideIcon } from "lucide-react";
import {
  Activity,
  Bell,
  Building2,
  CreditCard,
  FileText,
  Key,
  LayoutDashboard,
  Lock,
  LogOut,
  Search,
  Settings2,
  Shield,
  Tags,
  Ticket,
  User,
  UserCog,
  Users,
  UsersRound,
  Briefcase,
  ShoppingCart,
} from "lucide-react";
import { toast } from "sonner";
import { APP_NAME } from "@/lib/app-config";
import { useAuth } from "@/context/auth-context";
import { useI18n } from "@/context/i18n-context";
import { ChartPaletteProvider } from "@/context/chart-palette-context";
import { DashboardBrandingProvider } from "@/context/dashboard-branding-context";
import { useFeatureFlags } from "@/context/feature-flag-context";
import { AppFooter } from "@/components/app-footer";
import {
  DashboardCommandPalette,
  useCommandPaletteShortcut,
  type CommandPaletteItem,
} from "@/components/dashboard-command-palette";
import { DashboardAuthShell } from "@/components/dashboard-auth-shell";
import { LanguageSwitcher } from "@/components/language-switcher";
import { ThemeToggleIcon } from "@/components/theme-toggle-icon";
import { UserNavMenu } from "@/components/user-nav-menu";
import { NotificationHeaderMenu } from "@/components/notification-header-menu";
import { DashboardLogo } from "@/components/dashboard-logo";
import { Separator } from "@/components/ui/separator";
import { Button } from "@/components/ui/button";
import {
  Sidebar,
  SidebarContent,
  SidebarFooter,
  SidebarGroup,
  SidebarGroupContent,
  SidebarGroupLabel,
  SidebarHeader,
  SidebarInset,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
  SidebarProvider,
  SidebarRail,
  SidebarTrigger,
} from "@/components/ui/sidebar";
import { cn } from "@/lib/utils";

type NavLinkDef = {
  href: string;
  labelKey: string;
  fallback: string;
  flag?: string;
  permission?: string;
  icon: LucideIcon;
};

type NavSectionDef = {
  titleKey: string | null;
  titleFallback: string | null;
  items: NavLinkDef[];
};

const navSections: NavSectionDef[] = [
  {
    titleKey: "dashboard.nav.section_main",
    titleFallback: "Main",
    items: [
      { href: "/dashboard", labelKey: "dashboard.nav.overview", fallback: "Dashboard", icon: LayoutDashboard },
    ],
  },
  {
    titleKey: "dashboard.nav.section_access_control",
    titleFallback: "Access Control",
    items: [
      { href: "/dashboard/roles", labelKey: "dashboard.nav.roles", fallback: "Roles", icon: Shield, permission: "view.roles" },
      { href: "/dashboard/permissions", labelKey: "dashboard.nav.permissions", fallback: "Permissions", icon: Key, permission: "view.permissions" },
      { href: "/dashboard/users", labelKey: "dashboard.nav.users", fallback: "Users", icon: UserCog, permission: "view.users" },
    ],
  },
  {
    titleKey: "dashboard.nav.section_customer_management",
    titleFallback: "Customer Management",
    items: [
      { href: "/dashboard/brands", labelKey: "dashboard.nav.brands", fallback: "Brands", icon: Tags },
      { href: "/dashboard/branches", labelKey: "dashboard.nav.branches", fallback: "Branches", icon: Building2 },
    ],
  },
  {
    titleKey: "dashboard.nav.section_support",
    titleFallback: "Support",
    items: [
      { href: "/dashboard/tickets", labelKey: "dashboard.nav.tickets", fallback: "Tickets", icon: Ticket },
    ],
  },
  {
    titleKey: "dashboard.nav.section_account",
    titleFallback: "Account",
    items: [
      { href: "/dashboard/profile", labelKey: "dashboard.nav.my_profile", fallback: "My Profile", icon: User },
      { href: "/dashboard/settings", labelKey: "dashboard.nav.settings", fallback: "General Settings", icon: Settings2 },
      { href: "/dashboard/two-factor-auth", labelKey: "dashboard.nav.two_factor_auth", fallback: "Two-Factor Auth", icon: Shield },
    ],
  },
  {
    titleKey: "dashboard.nav.section_modules",
    titleFallback: "Modules",
    items: [
      { href: "/dashboard/modules/crm", labelKey: "dashboard.nav.crm", fallback: "CRM", icon: UsersRound, permission: "view.modules" },
      { href: "/dashboard/modules/hr", labelKey: "dashboard.nav.hr", fallback: "HR", icon: Briefcase, permission: "view.modules" },
      { href: "/dashboard/modules/pos", labelKey: "dashboard.nav.pos", fallback: "POS", icon: ShoppingCart, permission: "view.modules" },
    ],
  },
  {
    titleKey: "dashboard.nav.section_security",
    titleFallback: "Security",
    items: [
      { href: "/dashboard/login-attempts", labelKey: "dashboard.nav.login_attempts", fallback: "Login Attempts", icon: Shield, permission: "view.login_attempts" },
      { href: "/dashboard/activity-logs", labelKey: "dashboard.nav.activity_logs", fallback: "Activity Logs", icon: Activity, permission: "view.activity_logs" },
    ],
  },
];

function isMacLike(): boolean {
  if (typeof navigator === "undefined") return false;
  return /Mac|iPhone|iPad|iPod/i.test(navigator.platform) || navigator.userAgent.includes("Mac");
}

export default function DashboardLayout({ children }: { children: React.ReactNode }) {
  const { loading, isAuthenticated, logout, user } = useAuth();
  const { t, dir } = useI18n();
  const { isEnabled } = useFeatureFlags();
  const router = useRouter();
  const pathname = usePathname();
  const [paletteOpen, setPaletteOpen] = useState(false);

  const togglePalette = useCallback(() => {
    setPaletteOpen((o) => !o);
  }, []);

  useCommandPaletteShortcut(togglePalette);

  useEffect(() => {
    if (!loading && !isAuthenticated) {
      router.replace("/login");
    }
  }, [loading, isAuthenticated, router]);

  const userPerms = useMemo(() => new Set<string>((user as unknown as { permissions?: string[] })?.permissions ?? []), [user]);
  const hasPerm = useCallback((p?: string) => !p || userPerms.has(p) || userPerms.has("*"), [userPerms]);

  const visibleSections = useMemo(() => {
    return navSections
      .map((section) => ({
        ...section,
        items: section.items.filter((item) => (item.flag ? isEnabled(item.flag, true) : true) && hasPerm(item.permission)),
      }))
      .filter((section) => section.items.length > 0);
  }, [isEnabled, hasPerm]);

  const commandItems: CommandPaletteItem[] = useMemo(() => {
    const out: CommandPaletteItem[] = [];
    for (const section of visibleSections) {
      for (const item of section.items) {
        out.push({
          href: item.href,
          label: t(item.labelKey, item.fallback),
          icon: item.icon,
        });
      }
    }
    return out;
  }, [visibleSections, t]);

  if (loading) {
    return <DashboardAuthShell />;
  }

  if (!isAuthenticated) {
    return <DashboardAuthShell />;
  }

  const handleLogout = async () => {
    await logout();
    toast.success(t("dashboard.toast.signed_out", "Signed out successfully"));
  };

  const modKey = isMacLike() ? "⌘" : "Ctrl";

  return (
    <ChartPaletteProvider>
      <DashboardBrandingProvider>
      <DashboardCommandPalette open={paletteOpen} onOpenChange={setPaletteOpen} items={commandItems} />
      <SidebarProvider>
        <Sidebar collapsible="icon" dir={dir} side={dir === "rtl" ? "right" : "left"}>
          <SidebarHeader className="border-b border-sidebar-border">
            <SidebarMenu>
              <SidebarMenuItem>
                <SidebarMenuButton size="lg" render={<Link href="/dashboard" />} className="gap-2">
                  <div className="relative size-8 shrink-0 overflow-hidden rounded-lg ring-1 ring-sidebar-border">
                    <DashboardLogo />
                  </div>
                  <div className="grid flex-1 text-start text-sm leading-tight">
                    <span className="truncate font-semibold">{t("dashboard.app_name", APP_NAME)}</span>
                    <span className="truncate text-xs text-muted-foreground">
                      {t("dashboard.sidebar.subtitle", "Tenant Dashboard")}
                    </span>
                  </div>
                </SidebarMenuButton>
              </SidebarMenuItem>
              <SidebarMenuItem>
                <SidebarMenuButton
                  type="button"
                  tooltip={t("dashboard.nav.search_palette", "Search navigation")}
                  className="h-9 gap-2 bg-sidebar-accent/30 hover:bg-sidebar-accent"
                  onClick={() => setPaletteOpen(true)}
                >
                  <Search className="size-4 shrink-0 opacity-80" />
                  <span className="truncate text-muted-foreground group-data-[collapsible=icon]:hidden">
                    {t("dashboard.nav.search_placeholder", "Search…")}
                  </span>
                  <kbd
                    className={cn(
                      "pointer-events-none ms-auto inline-flex h-5 min-w-9 select-none items-center justify-center gap-0.5 rounded border border-sidebar-border bg-sidebar px-1 font-mono text-[10px] font-medium text-muted-foreground opacity-100 group-data-[collapsible=icon]:hidden",
                      dir === "rtl" && "ms-0 me-auto",
                    )}
                  >
                    {modKey}
                    <span className="tracking-tighter">K</span>
                  </kbd>
                </SidebarMenuButton>
              </SidebarMenuItem>
            </SidebarMenu>
          </SidebarHeader>
          <SidebarContent>
            {visibleSections.map((section, si) => (
              <SidebarGroup key={si}>
                {section.titleKey ? (
                  <SidebarGroupLabel className="text-xs font-semibold uppercase tracking-wide text-muted-foreground">
                    {t(section.titleKey, section.titleFallback ?? "")}
                  </SidebarGroupLabel>
                ) : null}
                <SidebarGroupContent>
                  <SidebarMenu>
                    {section.items.map((item) => {
                      const Icon = item.icon;
                      const label = t(item.labelKey, item.fallback);
                      return (
                        <SidebarMenuItem key={item.href}>
                          <SidebarMenuButton
                            isActive={pathname === item.href}
                            tooltip={label}
                            render={<Link href={item.href} />}
                          >
                            <Icon />
                            <span>{label}</span>
                          </SidebarMenuButton>
                        </SidebarMenuItem>
                      );
                    })}
                  </SidebarMenu>
                </SidebarGroupContent>
              </SidebarGroup>
            ))}
          </SidebarContent>
          <SidebarFooter className="border-t border-sidebar-border p-2 group-data-[collapsible=icon]:gap-1.5">
            <UserNavMenu displayName={user?.name} onLogout={() => void handleLogout()} variant="sidebar" />
            <div className="flex min-w-0 gap-2 group-data-[collapsible=icon]:flex-col group-data-[collapsible=icon]:gap-1.5">
              <Button
                type="button"
                variant="outline"
                size="sm"
                title={t("dashboard.actions.logout", "Logout")}
                className="h-9 min-w-0 flex-1 gap-1.5 border-sidebar-border px-2 text-xs group-data-[collapsible=icon]:h-9 group-data-[collapsible=icon]:w-full group-data-[collapsible=icon]:flex-none group-data-[collapsible=icon]:justify-center group-data-[collapsible=icon]:px-0 group-data-[collapsible=icon]:gap-0"
                onClick={() => {
                  void handleLogout();
                }}
              >
                <LogOut className="size-3.5 shrink-0" />
                <span className="truncate group-data-[collapsible=icon]:sr-only">
                  {t("dashboard.actions.logout", "Logout")}
                </span>
              </Button>
              <Button
                type="button"
                variant="outline"
                size="sm"
                title={t("dashboard.actions.lock_screen", "Lock screen")}
                className="h-9 min-w-0 flex-1 gap-1.5 border-sidebar-border px-2 text-xs group-data-[collapsible=icon]:h-9 group-data-[collapsible=icon]:w-full group-data-[collapsible=icon]:flex-none group-data-[collapsible=icon]:justify-center group-data-[collapsible=icon]:px-0 group-data-[collapsible=icon]:gap-0"
                onClick={() => router.push("/lock-screen")}
              >
                <Lock className="size-3.5 shrink-0" />
                <span className="truncate group-data-[collapsible=icon]:sr-only">
                  {t("dashboard.actions.lock_screen", "Lock screen")}
                </span>
              </Button>
            </div>
          </SidebarFooter>
          <SidebarRail />
        </Sidebar>
        <SidebarInset className="flex max-h-svh flex-col overflow-hidden">
          <header className="flex h-14 shrink-0 items-center justify-between gap-3 border-b border-border bg-background/95 px-4 backdrop-blur supports-backdrop-filter:bg-background/80">
            <div className="flex min-w-0 items-center gap-3">
              <SidebarTrigger className="-ms-1 shrink-0" />
              <Link href="/dashboard" className="flex shrink-0 items-center gap-2">
                <DashboardLogo className="shadow-sm" />
                <span className="hidden font-semibold tracking-tight sm:inline">{t("dashboard.app_name", APP_NAME)}</span>
              </Link>
              <Separator orientation="vertical" className="hidden h-6 sm:block" />
            </div>
            <div className="flex min-w-0 shrink-0 items-center gap-2">
              {isEnabled("dashboard.notifications", true) ? <NotificationHeaderMenu /> : null}
              <LanguageSwitcher />
              <ThemeToggleIcon />
              <UserNavMenu displayName={user?.name} onLogout={() => void handleLogout()} variant="header" />
            </div>
          </header>
          <div className="flex min-h-0 flex-1 flex-col overflow-y-auto">
            <div className="flex flex-1 flex-col gap-4 p-4 md:p-6">
              <div className="flex-1 rounded-xl border border-border/80 bg-card p-4 shadow-sm md:p-6">{children}</div>
            </div>
            <AppFooter />
          </div>
        </SidebarInset>
      </SidebarProvider>
      </DashboardBrandingProvider>
    </ChartPaletteProvider>
  );
}
