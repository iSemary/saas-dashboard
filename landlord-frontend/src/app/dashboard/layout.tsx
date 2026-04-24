"use client";

import Link from "next/link";
import { usePathname, useRouter } from "next/navigation";
import { useCallback, useEffect, useMemo, useState } from "react";
import type { LucideIcon } from "lucide-react";
import {
  Activity,
  Bell,
  BookOpen,
  Heart,
  Building2,
  Calendar,
  CreditCard,
  DatabaseBackup,
  FileText,
  Flag,
  FolderTree,
  Globe2,
  Home,
  Key,
  Languages,
  Layers,
  LayoutDashboard,
  Lock,
  LogOut,
  Mail,
  Map,
  Palette,
  Rocket,
  ScrollText,
  Search,
  Server,
  Settings2,
  Shield,
  Tags,
  TagsIcon,
  Wrench,
  UserCog,
  Users,
  Send,
  ExternalLink,
  FolderOpen,
  Ban,
  Code,
  FileDiff,
  Box,
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
  icon: LucideIcon;
  permission?: string;
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
    titleKey: "dashboard.nav.section_account_management",
    titleFallback: "Account Management",
    items: [
      { href: "/dashboard/system-users", labelKey: "dashboard.nav.system_users", fallback: "System Users", icon: UserCog, permission: "view.system_users" },
      { href: "/dashboard/tenants", labelKey: "dashboard.nav.tenants", fallback: "Tenants", icon: Building2, permission: "view.tenants" },
      { href: "/dashboard/brands", labelKey: "dashboard.nav.brands", fallback: "Brands", icon: Tags, permission: "view.brands" },
      { href: "/dashboard/branches", labelKey: "dashboard.nav.branches", fallback: "Branches", icon: Building2, permission: "view.branches" },
    ],
  },
  {
    titleKey: "dashboard.nav.section_mailing",
    titleFallback: "Mailing",
    items: [
      { href: "/dashboard/email-campaigns", labelKey: "dashboard.nav.email_campaigns", fallback: "Email Campaigns", icon: Mail },
      { href: "/dashboard/email-templates", labelKey: "dashboard.nav.email_templates", fallback: "Email Templates", icon: Mail },
      { href: "/dashboard/email-credentials", labelKey: "dashboard.nav.email_credentials", fallback: "Email Credentials", icon: Key },
      { href: "/dashboard/email-recipients", labelKey: "dashboard.nav.email_recipients", fallback: "Email Recipients", icon: Users },
      { href: "/dashboard/email-groups", labelKey: "dashboard.nav.email_groups", fallback: "Email Groups", icon: UserCog },
      { href: "/dashboard/email-subscribers", labelKey: "dashboard.nav.email_subscribers", fallback: "Email Subscribers", icon: Users },
      { href: "/dashboard/email-log", labelKey: "dashboard.nav.email_log", fallback: "Email Log", icon: Activity },
      { href: "/dashboard/compose-email", labelKey: "dashboard.nav.compose_email", fallback: "Compose Email", icon: Send },
    ],
  },
  {
    titleKey: "dashboard.nav.section_locale",
    titleFallback: "Locale",
    items: [
      { href: "/dashboard/languages", labelKey: "dashboard.nav.languages", fallback: "Languages", icon: Languages },
      { href: "/dashboard/translations", labelKey: "dashboard.nav.translations", fallback: "Translations", icon: Globe2 },
    ],
  },
  {
    titleKey: "dashboard.nav.section_geography",
    titleFallback: "Geography",
    items: [
      { href: "/dashboard/countries", labelKey: "dashboard.nav.countries", fallback: "Countries", icon: Flag },
      { href: "/dashboard/provinces", labelKey: "dashboard.nav.provinces", fallback: "Provinces", icon: Map },
      { href: "/dashboard/cities", labelKey: "dashboard.nav.cities", fallback: "Cities", icon: Building2 },
      { href: "/dashboard/towns", labelKey: "dashboard.nav.towns", fallback: "Towns", icon: Home },
      { href: "/dashboard/streets", labelKey: "dashboard.nav.streets", fallback: "Streets", icon: Map },
    ],
  },
  {
    titleKey: "dashboard.nav.section_utilities",
    titleFallback: "Utilities",
    items: [
      { href: "/dashboard/categories", labelKey: "dashboard.nav.categories", fallback: "Categories", icon: FolderTree },
      { href: "/dashboard/tags", labelKey: "dashboard.nav.tags", fallback: "Tags", icon: TagsIcon },
      { href: "/dashboard/types", labelKey: "dashboard.nav.types", fallback: "Types", icon: Layers },
      { href: "/dashboard/industries", labelKey: "dashboard.nav.industries", fallback: "Industries", icon: Building2 },
      { href: "/dashboard/currencies", labelKey: "dashboard.nav.currencies", fallback: "Currencies", icon: CreditCard },
      { href: "/dashboard/units", labelKey: "dashboard.nav.units", fallback: "Units", icon: Wrench },
    ],
  },
  {
    titleKey: "dashboard.nav.section_payments",
    titleFallback: "Payments",
    items: [
      { href: "/dashboard/payment-methods", labelKey: "dashboard.nav.payment_methods", fallback: "Payment Methods", icon: CreditCard },
      { href: "/dashboard/payment-analytics", labelKey: "dashboard.nav.payment_analytics", fallback: "Payment Analytics", icon: Activity },
    ],
  },
  {
    titleKey: "dashboard.nav.section_subscriptions",
    titleFallback: "Subscriptions",
    items: [
      { href: "/dashboard/subscriptions", labelKey: "dashboard.nav.subscriptions", fallback: "Subscriptions", icon: Calendar },
      { href: "/dashboard/plans", labelKey: "dashboard.nav.plans", fallback: "Plans", icon: ScrollText },
    ],
  },
  {
    titleKey: "dashboard.nav.section_authorizations",
    titleFallback: "Authorizations",
    items: [
      { href: "/dashboard/permissions", labelKey: "dashboard.nav.permissions", fallback: "Permissions", icon: Key, permission: "view.permissions" },
      { href: "/dashboard/roles", labelKey: "dashboard.nav.roles", fallback: "Roles", icon: Shield, permission: "view.roles" },
      { href: "/dashboard/permission-groups", labelKey: "dashboard.nav.permission_groups", fallback: "Permission Groups", icon: Shield, permission: "view.permission_groups" },
      { href: "/dashboard/profile", labelKey: "dashboard.nav.profile", fallback: "Profile", icon: UserCog },
    ],
  },
  {
    titleKey: "dashboard.nav.section_system_settings",
    titleFallback: "System Settings",
    items: [
      { href: "/dashboard/announcements", labelKey: "dashboard.nav.announcements", fallback: "Announcements", icon: Bell },
      { href: "/dashboard/static-pages", labelKey: "dashboard.nav.static_pages", fallback: "Static Pages", icon: ScrollText },
      { href: "/dashboard/releases", labelKey: "dashboard.nav.releases", fallback: "Releases", icon: Rocket },
      { href: "/dashboard/modules", labelKey: "dashboard.nav.modules", fallback: "Modules", icon: Layers },
    ],
  },
  {
    titleKey: "dashboard.nav.section_development",
    titleFallback: "Development",
    items: [
      { href: "/dashboard/system-status", labelKey: "dashboard.nav.system_status", fallback: "System Status", icon: Server, permission: "view.system_status" },
      { href: "/dashboard/files", labelKey: "dashboard.nav.files", fallback: "Files", icon: FileText, permission: "view.files" },
      { href: "/dashboard/configurations", labelKey: "dashboard.nav.configurations", fallback: "Configurations", icon: Settings2, permission: "view.configurations" },
      { href: "/dashboard/backups", labelKey: "dashboard.nav.backups", fallback: "Backups", icon: DatabaseBackup, permission: "view.backups" },
      { href: "/dashboard/feature-flags", labelKey: "dashboard.nav.feature_flags", fallback: "Feature Flags", icon: Flag, permission: "view.feature_flags" },
      { href: "/dashboard/file-manager", labelKey: "dashboard.nav.file_manager", fallback: "File Manager", icon: FolderOpen, permission: "view.file_manager" },
      { href: "/dashboard/ip-blacklists", labelKey: "dashboard.nav.ip_blacklists", fallback: "IP Blacklists", icon: Ban, permission: "view.ip_blacklists" },
      { href: "/dashboard/code-builder", labelKey: "dashboard.nav.code_builder", fallback: "Code Builder", icon: Code, permission: "view.code_builder" },
      { href: "/dashboard/env-diff", labelKey: "dashboard.nav.env_diff", fallback: "Env Diff", icon: FileDiff, permission: "view.env_diff" },
      { href: "/dashboard/entities", labelKey: "dashboard.nav.modules_entities", fallback: "Modules Entities", icon: Box, permission: "view.entities" },
    ],
  },
  {
    titleKey: "dashboard.nav.section_system_monitoring",
    titleFallback: "System Monitoring",
    items: [
      { href: "/dashboard/monitoring", labelKey: "dashboard.nav.monitoring", fallback: "Monitoring", icon: Activity, permission: "view.monitoring" },
      { href: "/dashboard/system-health", labelKey: "dashboard.nav.system_health", fallback: "System Health", icon: Heart, permission: "view.system_health" },
      { href: "/dashboard/tenant-monitoring", labelKey: "dashboard.nav.tenant_monitoring", fallback: "Tenant Monitoring", icon: Building2, permission: "view.tenant_monitoring" },
      { href: "/dashboard/activity-logs", labelKey: "dashboard.nav.activity_logs", fallback: "Activity Logs", icon: ScrollText },
    ],
  },
  {
    titleKey: "dashboard.nav.section_documentation",
    titleFallback: "Documentation",
    items: [
      { href: "/dashboard/documentation", labelKey: "dashboard.nav.documentation", fallback: "Documentation", icon: BookOpen },
      { href: "/dashboard/tickets", labelKey: "dashboard.nav.tickets", fallback: "Tickets", icon: FileText },
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

  const userPerms = useMemo(() => new Set(user?.permissions ?? []), [user]);

  const visibleSections = useMemo(() => {
    return navSections
      .map((section) => ({
        ...section,
        items: section.items.filter((item) => {
          if (item.flag && !isEnabled(item.flag, true)) return false;
          if (item.permission && !userPerms.has(item.permission)) return false;
          return true;
        }),
      }))
      .filter((section) => section.items.length > 0);
  }, [isEnabled, userPerms]);

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
                      {t("dashboard.sidebar.subtitle", "Landlord Dashboard")}
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
            </div>
            <div className="flex min-w-0 shrink-0 items-center gap-2">
              {isEnabled("dashboard.notifications", true) ? <NotificationHeaderMenu /> : null}
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
