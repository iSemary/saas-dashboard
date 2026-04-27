"use client";

import Link from "next/link";
import { usePathname, useRouter } from "next/navigation";
import { useCallback, useEffect, useMemo, useRef, useState } from "react";
import type { LucideIcon } from "lucide-react";
import {
  Activity,
  Building2,
  ChevronLeft,
  CreditCard,
  Key,
  LayoutDashboard,
  Lock,
  LogOut,
  Search,
  Settings2,
  Shield,
  ShoppingBag,
  ShoppingCart,
  Tags,
  Ticket,
  User,
  UserCog,
} from "lucide-react";
import { toast } from "sonner";
import { APP_NAME } from "@/lib/app-config";
import { useAuth } from "@/context/auth-context";
import { useI18n } from "@/context/i18n-context";
import { useModule, type SubscribedModule } from "@/context/module-context";
import { ChartPaletteProvider } from "@/context/chart-palette-context";
import { DashboardBrandingProvider } from "@/context/dashboard-branding-context";
import { useFeatureFlags } from "@/context/feature-flag-context";
import { AppFooter } from "@/components/app-footer";
import { DashboardCommandPalette, useCommandPaletteShortcut, type CommandPaletteItem } from "@/components/dashboard-command-palette";
import { DashboardAuthShell } from "@/components/dashboard-auth-shell";
import { ThemeToggleIcon } from "@/components/theme-toggle-icon";
import { UserNavMenu } from "@/components/user-nav-menu";
import { NotificationHeaderMenu } from "@/components/notification-header-menu";
import { DashboardLogo } from "@/components/dashboard-logo";
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
import { resolveIcon } from "@/lib/lucide-icon-map";
import { useAnimation } from "@/context/animation-context";
import { BrandFilterProvider, useBrandFilter } from "@/context/brand-filter-context";
import { Fades } from "@/components/animate-ui/primitives/effects/fade";
import { Badge } from "@/components/ui/badge";
import { X } from "lucide-react";
import ErrorPage from "@/app/error/page";

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
    ],
  },
  {
    titleKey: "dashboard.nav.section_billing",
    titleFallback: "Billing",
    items: [
      { href: "/dashboard/billing", labelKey: "dashboard.nav.billing_overview", fallback: "Overview", icon: CreditCard },
      { href: "/dashboard/billing/invoices", labelKey: "dashboard.nav.invoices", fallback: "Invoices", icon: ShoppingCart },
      { href: "/dashboard/billing/payments", labelKey: "dashboard.nav.payments", fallback: "Payments", icon: CreditCard },
      { href: "/dashboard/billing/payment-methods", labelKey: "dashboard.nav.payment_methods", fallback: "Payment Methods", icon: CreditCard },
      { href: "/dashboard/billing/modules", labelKey: "dashboard.nav.billing_modules", fallback: "Modules & Add-ons", icon: ShoppingBag },
    ],
  },
  // {
  //   titleKey: "dashboard.nav.section_modules",
  //   titleFallback: "Modules",
  //   items: [] as NavLinkDef[],
  // },
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

// Component to show active brand filter in header
function BrandHeaderIndicator() {
  const { brandFilter, brandError, clearBrandFilter } = useBrandFilter();
  const router = useRouter();
  const pathname = usePathname();
  const { t } = useI18n();

  // Show error if there's a brand resolution error
  if (brandError) {
    return (
      <div className="flex items-center gap-2">
        <Badge
          variant="destructive"
          className="flex items-center gap-1.5 px-2 py-1"
        >
          <span className="max-w-[300px] truncate text-xs font-medium">
            {brandError}
          </span>
          <button
            onClick={() => {
              clearBrandFilter();
              const pathParts = pathname.split("/");
              const moduleKey = pathParts[3];
              if (moduleKey) {
                router.push(`/dashboard/modules/${moduleKey}`);
              } else {
                router.push("/dashboard");
              }
            }}
            className="ml-1 rounded-full p-0.5 hover:bg-destructive/20 transition-colors"
            title={t("dashboard.brand.go_back", "Go back")}
          >
            <X className="size-3" />
          </button>
        </Badge>
      </div>
    );
  }

  // Only show when inside a module with brand filter active
  if (!brandFilter?.id) return null;

  // Check if we're inside a module page (brand-scoped or regular)
  const isInModule = pathname.startsWith("/dashboard/modules/");
  if (!isInModule) return null;

  // Extract module key from path
  const pathParts = pathname.split("/");
  const moduleKey = pathParts[3]; // /dashboard/modules/[module]/...

  // Build URL to clear filter (go to non-brand-scoped module page)
  const clearUrl = `/dashboard/modules/${moduleKey}`;

  return (
    <div className="flex items-center gap-2">
      <Badge
        variant="secondary"
        className="flex items-center gap-1.5 px-2 py-1 bg-primary/10 text-primary hover:bg-primary/20"
      >
        <Building2 className="size-3" />
        <span className="max-w-[120px] truncate text-xs font-medium">
          {brandFilter.name}
        </span>
        <button
          onClick={() => {
            clearBrandFilter();
            router.push(clearUrl);
          }}
          className="ml-1 rounded-full p-0.5 hover:bg-primary/20 transition-colors"
          title={t("dashboard.brand.clear_filter", "Clear brand filter")}
        >
          <X className="size-3" />
        </button>
      </Badge>
    </div>
  );
}

export default function DashboardLayout({ children }: { children: React.ReactNode }) {
  const { loading, isAuthenticated, logout, user } = useAuth();
  const { t, dir } = useI18n();
  const { isEnabled } = useFeatureFlags();
  const { enabled: animationsEnabled } = useAnimation();
  const { subscribedModules, fetchModuleDetail } = useModule();
  const router = useRouter();
  const pathname = usePathname();
  const [paletteOpen, setPaletteOpen] = useState(false);
  const { brandError } = useBrandFilter();
  const fetchingModulesRef = useRef<Set<string>>(new Set());

  const togglePalette = useCallback(() => {
    setPaletteOpen((o) => !o);
  }, []);

  useCommandPaletteShortcut(togglePalette);

  useEffect(() => {
    if (!loading && !isAuthenticated) {
      router.replace("/login");
    }
  }, [loading, isAuthenticated, router]);

  // Helper to normalize module keys (hyphens to underscores like backend)
  const normalizeModuleKey = useCallback((key: string) => key.replace(/-/g, '_'), []);

  // Fetch module detail when active module has no navigation
  useEffect(() => {
    const activeModuleKey = pathname.startsWith("/dashboard/modules/")
      ? pathname.split("/")[3] ?? null
      : null;

    console.log("[Debug] Active module key:", activeModuleKey);
    console.log("[Debug] Subscribed modules count:", subscribedModules.length);
    console.log("[Debug] Subscribed modules keys:", subscribedModules.map(m => m.module_key));

    if (!activeModuleKey) return;

    const normalizedKey = normalizeModuleKey(activeModuleKey);
    const activeMod = subscribedModules.find((m) => m.module_key === activeModuleKey || m.module_key === normalizedKey);
    console.log("[Debug] Found active module:", activeMod ? { key: activeMod.module_key, hasNav: !!activeMod.navigation?.length } : null);

    if (activeMod && !activeMod.navigation?.length && !fetchingModulesRef.current.has(normalizedKey)) {
      console.log("[Debug] Fetching module detail for:", normalizedKey);
      fetchingModulesRef.current.add(normalizedKey);
      fetchModuleDetail(normalizedKey).then((result) => {
        console.log("[Debug] Fetch result:", result ? { key: result.module_key, hasNav: !!result.navigation?.length } : null);
      }).finally(() => {
        fetchingModulesRef.current.delete(normalizedKey);
      });
    }
  }, [pathname, subscribedModules, fetchModuleDetail, normalizeModuleKey]);

  const userPerms = useMemo(() => new Set<string>((user as unknown as { permissions?: string[] })?.permissions ?? []), [user]);
  const hasPerm = useCallback((p?: string) => !p || userPerms.has(p) || userPerms.has("*"), [userPerms]);

  const visibleSections = useMemo(() => {
    const moduleItems: NavLinkDef[] = (subscribedModules as SubscribedModule[]).map((mod) => ({
      href: mod.route ?? `/dashboard/modules/${mod.module_key}`,
      labelKey: `dashboard.nav.${mod.module_key}`,
      fallback: mod.name,
      icon: resolveIcon(mod.icon),
    }));

    return navSections
      .map((section) => {
        if (section.titleKey === "dashboard.nav.section_modules") {
          return { ...section, items: moduleItems };
        }
        return {
          ...section,
          items: section.items.filter((item) => (item.flag ? isEnabled(item.flag, true) : true) && hasPerm(item.permission)),
        };
      })
      .filter((section) => section.items.length > 0);
  }, [isEnabled, hasPerm, subscribedModules]);

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
            {/* Navigation Content */}
            {(() => {
              const activeModuleKey = pathname.startsWith("/dashboard/modules/")
                ? pathname.split("/")[3] ?? null
                : null;
              const normalizedKey = activeModuleKey ? activeModuleKey.replace(/-/g, '_') : null;
              const activeMod = activeModuleKey
                ? (subscribedModules as SubscribedModule[]).find((m) => m.module_key === activeModuleKey || m.module_key === normalizedKey)
                : null;
              
              // If we are within a module, show the module's sub-sidebar and a back button
              if (activeMod) {
                return (
                  <>
                    <SidebarGroup>
                      <SidebarGroupContent>
                        <SidebarMenu>
                          <SidebarMenuItem>
                            <SidebarMenuButton
                              tooltip={t("dashboard.nav.back_to_main", "Back to Main Menu")}
                              render={<Link href="/dashboard" />}
                              className="text-muted-foreground hover:bg-sidebar-accent/50 mb-2"
                            >
                              <ChevronLeft className="size-4" />
                              <span className="font-medium">{t("dashboard.nav.back_to_main", "Back to Main Menu")}</span>
                            </SidebarMenuButton>
                          </SidebarMenuItem>
                        </SidebarMenu>
                      </SidebarGroupContent>
                    </SidebarGroup>
                    
                    {(() => {
                      if (!activeMod.navigation?.length) {
                        // Show loading state while fetching navigation
                        return (
                          <SidebarGroup>
                            <SidebarGroupContent>
                              <SidebarMenu>
                                <SidebarMenuItem>
                                  <SidebarMenuButton disabled className="text-muted-foreground">
                                    <span className="size-4 animate-pulse rounded-full bg-muted" />
                                    <span>Loading navigation...</span>
                                  </SidebarMenuButton>
                                </SidebarMenuItem>
                              </SidebarMenu>
                            </SidebarGroupContent>
                          </SidebarGroup>
                        );
                      }

                      // Group by section
                      const groupedNav = activeMod.navigation.reduce((acc, nav) => {
                        const section = nav.section || activeMod.name;
                        if (!acc[section]) acc[section] = [];
                        acc[section].push(nav);
                        return acc;
                      }, {} as Record<string, typeof activeMod.navigation>);

                      return (
                        <>
                          {Object.entries(groupedNav).map(([sectionTitle, items]) => (
                            <SidebarGroup key={sectionTitle}>
                              <SidebarGroupLabel className="text-xs font-semibold uppercase tracking-wide text-muted-foreground">
                                {sectionTitle}
                              </SidebarGroupLabel>
                              <SidebarGroupContent>
                                <SidebarMenu>
                                  {items.map((nav) => {
                                    const NavIcon = resolveIcon(nav.icon);
                                    // Get brand-scoped route if brand filter is active
                                    const brandScopedRoute = (() => {
                                      const brandSlug = pathname.split('/')[4]; // /dashboard/modules/[module]/[brand]/...
                                      // Dynamically check if brandSlug is a known module key (not a brand)
                                      const moduleKeys = new Set((subscribedModules as SubscribedModule[]).map(m => m.module_key));
                                      const isBrandScoped = brandSlug && !moduleKeys.has(brandSlug) && brandSlug !== 'new';
                                      if (isBrandScoped && nav.route.startsWith(`/dashboard/modules/${activeModuleKey}`)) {
                                        return nav.route.replace(
                                          `/dashboard/modules/${activeModuleKey}`,
                                          `/dashboard/modules/${activeModuleKey}/${brandSlug}`
                                        );
                                      }
                                      return nav.route;
                                    })();
                                    return (
                                      <SidebarMenuItem key={nav.key}>
                                        <SidebarMenuButton
                                          isActive={pathname === nav.route || pathname === brandScopedRoute}
                                          tooltip={nav.label}
                                          render={<Link href={brandScopedRoute} />}
                                        >
                                          <NavIcon />
                                          <span>{nav.label}</span>
                                        </SidebarMenuButton>
                                      </SidebarMenuItem>
                                    );
                                  })}
                                </SidebarMenu>
                              </SidebarGroupContent>
                            </SidebarGroup>
                          ))}
                        </>
                      );
                    })()}
                  </>
                );
              }

              // Otherwise, show the normal dashboard navigation
              return (
                <>
                  {animationsEnabled ? (
                    <Fades holdDelay={75}>
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
                    </Fades>
                  ) : (
                    visibleSections.map((section, si) => (
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
                    ))
                  )}
                </>
              );
            })()}
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
              <BrandHeaderIndicator />
            </div>
            <div className="flex min-w-0 shrink-0 items-center gap-2">
              {isEnabled("dashboard.notifications", true) ? <NotificationHeaderMenu /> : null}
              <ThemeToggleIcon />
              <UserNavMenu displayName={user?.name} onLogout={() => void handleLogout()} variant="header" />
            </div>
          </header>
          <div className="flex min-h-0 flex-1 flex-col overflow-y-auto">
            <div className="flex flex-1 flex-col gap-4 p-4 md:p-6">
              <BrandFilterProvider>
                {brandError ? (
                  <ErrorPage
                    title="Brand Error"
                    description="There was an issue with the brand specified in the URL."
                    errorDetails={brandError}
                    showBackButton={true}
                    showHomeButton={true}
                  />
                ) : (
                  <div className="flex-1 rounded-xl border border-border/80 bg-card p-4 shadow-sm md:p-6">{children}</div>
                )}
              </BrandFilterProvider>
            </div>
            <AppFooter />
          </div>
        </SidebarInset>
      </SidebarProvider>
      </DashboardBrandingProvider>
    </ChartPaletteProvider>
  );
}
