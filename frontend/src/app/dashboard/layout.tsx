"use client"

import Link from "next/link"
import { usePathname, useRouter, useSearchParams } from "next/navigation"
import { useEffect, useState } from "react"
import { Button } from "@/components/ui/button"
import { Card } from "@/components/ui/card"
import {
  Tooltip,
  TooltipContent,
  TooltipProvider,
  TooltipTrigger,
} from "@/components/ui/tooltip"
import { useAuth } from "@/context/auth-context"
import { toast } from "sonner"
import {
  Settings as SettingsIcon,
  Home,
  Shield,
  Key,
  Users,
  ShieldCheck,
  ChevronDown,
  ChevronRight,
  UserCircle,
  CreditCard,
  FileText,
  Activity,
} from "lucide-react"

function NavIconLink({
  href,
  icon: Icon,
  label,
}: {
  href: string
  icon: React.ComponentType<{ className?: string }>
  label: string
}) {
  const pathname = usePathname()
  const hrefPath = href.split('#')[0]
  const active = hrefPath === "/dashboard" 
    ? pathname === hrefPath 
    : pathname === hrefPath || pathname.startsWith(hrefPath + "/")

  return (
    <Tooltip>
      <TooltipTrigger asChild>
        <Link
          href={href}
          className={[
            "flex items-center justify-center rounded-md p-2 text-sm font-medium transition-colors",
            active
              ? "bg-muted text-foreground"
              : "text-muted-foreground hover:bg-muted/60 hover:text-foreground",
          ].join(" ")}
        >
          <Icon className="h-5 w-5" />
        </Link>
      </TooltipTrigger>
      <TooltipContent>
        <p>{label}</p>
      </TooltipContent>
    </Tooltip>
  )
}

function SidebarNavLink({
  href,
  icon: Icon,
  label,
}: {
  href: string
  icon: React.ComponentType<{ className?: string }>
  label: string
}) {
  const pathname = usePathname()
  const hrefPath = href.split('#')[0]
  const active = hrefPath === "/dashboard" 
    ? pathname === hrefPath 
    : pathname === hrefPath || pathname.startsWith(hrefPath + "/")

  return (
    <Link
      href={href}
      className={[
        "flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition-colors",
        active
          ? "bg-muted text-foreground"
          : "text-muted-foreground hover:bg-muted/60 hover:text-foreground",
      ].join(" ")}
    >
      <Icon className="h-5 w-5" />
      <span>{label}</span>
    </Link>
  )
}

function SettingsSubmenu({ user }: { user: { two_factor_enabled?: boolean } | null }) {
  const pathname = usePathname()
  const [isOpen, setIsOpen] = useState(pathname.startsWith("/dashboard/settings"))
  
  const isSettingsActive = pathname.startsWith("/dashboard/settings")
  const isTwoFactorActive = pathname === "/dashboard/settings/two-factor"

  return (
    <div>
      <button
        onClick={() => setIsOpen(!isOpen)}
        className={[
          "flex w-full items-center justify-between gap-3 rounded-md px-3 py-2 text-sm font-medium transition-colors",
          isSettingsActive
            ? "bg-muted text-foreground"
            : "text-muted-foreground hover:bg-muted/60 hover:text-foreground",
        ].join(" ")}
      >
        <div className="flex items-center gap-3">
          <SettingsIcon className="h-5 w-5" />
          <span>Settings</span>
        </div>
        {isOpen ? (
          <ChevronDown className="h-4 w-4" />
        ) : (
          <ChevronRight className="h-4 w-4" />
        )}
      </button>
      {isOpen && (
        <div className="ml-6 mt-1 space-y-1 border-l pl-3">
          <Link
            href="/dashboard/settings"
            className={[
              "flex items-center gap-3 rounded-md px-3 py-2 text-sm transition-colors",
              pathname === "/dashboard/settings"
                ? "bg-muted text-foreground"
                : "text-muted-foreground hover:bg-muted/60 hover:text-foreground",
            ].join(" ")}
          >
            <span>General</span>
          </Link>
          <Link
            href="/dashboard/settings/two-factor"
            className={[
              "flex items-center gap-3 rounded-md px-3 py-2 text-sm transition-colors",
              isTwoFactorActive
                ? "bg-muted text-foreground"
                : "text-muted-foreground hover:bg-muted/60 hover:text-foreground",
            ].join(" ")}
          >
            <ShieldCheck className="h-4 w-4" />
            <span>Two-Factor Auth</span>
            {!user?.two_factor_enabled && (
              <span className="ml-auto rounded-full bg-destructive px-2 py-0.5 text-xs text-destructive-foreground">
                Required
              </span>
            )}
          </Link>
        </div>
      )}
    </div>
  )
}

export default function DashboardLayout({ children }: { children: React.ReactNode }) {
  const { user, isAuthenticated, loading, logout } = useAuth()
  const router = useRouter()
  const pathname = usePathname()
  const searchParams = useSearchParams()

  useEffect(() => {
    if (!loading && !isAuthenticated) router.replace("/login")
  }, [loading, isAuthenticated, router])

  // Force 2FA setup if not enabled (except on 2FA setup page itself)
  useEffect(() => {
    if (!loading && isAuthenticated && user && !user.two_factor_enabled) {
      const currentPath = pathname
      // Allow access to 2FA setup page and login/verify-2fa
      if (
        !currentPath.startsWith("/dashboard/settings/two-factor") &&
        !currentPath.startsWith("/login/verify-2fa") &&
        currentPath !== "/login"
      ) {
        router.push("/dashboard/settings/two-factor")
        toast.warning("Two-factor authentication is required. Please enable it to continue.")
      }
    }
  }, [loading, isAuthenticated, user, pathname, router])

  useEffect(() => {
    const oauth = searchParams.get("oauth")
    const platform = searchParams.get("platform")
    if (oauth === "success") toast.success(`Connected ${platform ?? "channel"}`)
  }, [searchParams])

  if (loading) return null
  if (!isAuthenticated) return null

  return (
    <TooltipProvider>
      <div className="min-h-screen bg-background">
        <header className="sticky top-0 z-10 border-b bg-background/80 backdrop-blur">
        <div className="mx-auto flex max-w-[1600px] items-center justify-between px-4 py-3">
          <div className="flex items-center gap-3">
            <Link href="/dashboard" className="text-sm font-semibold">
              Tenant Dashboard
            </Link>
            <nav className="hidden items-center gap-1 md:flex">
              <NavIconLink
                href="/dashboard/customers"
                icon={UserCircle}
                label="Customers"
              />
              <NavIconLink
                href="/dashboard/subscriptions"
                icon={CreditCard}
                label="Subscriptions"
              />
              <NavIconLink
                href="/dashboard/activity-logs"
                icon={Activity}
                label="Activity Logs"
              />
              <NavIconLink
                href="/dashboard/settings"
                icon={SettingsIcon}
                label="Settings"
              />
              {user?.permissions?.includes('view-users') && (
                <NavIconLink
                  href="/dashboard/users"
                  icon={Users}
                  label="Users"
                />
              )}
              {user?.permissions?.includes('view-roles') && (
                <NavIconLink
                  href="/dashboard/roles"
                  icon={Shield}
                  label="Roles"
                />
              )}
              {user?.permissions?.includes('view-permissions') && (
                <NavIconLink
                  href="/dashboard/permissions"
                  icon={Key}
                  label="Permissions"
                />
              )}
            </nav>
          </div>
          <div className="flex items-center gap-3">
            <span className="hidden text-sm text-muted-foreground md:inline">
              {user?.name}
            </span>
            <Button variant="outline" size="sm" onClick={logout}>
              Logout
            </Button>
          </div>
        </div>
      </header>

      <main className="mx-auto max-w-[1600px] px-4 py-6">
        <div className="grid gap-6 md:grid-cols-[220px_1fr]">
          <aside className="hidden md:block">
            <Card className="p-2">
              <div className="grid gap-1">
                <SidebarNavLink
                  href="/dashboard"
                  icon={Home}
                  label="Home"
                />
                <SidebarNavLink
                  href="/dashboard/customers"
                  icon={UserCircle}
                  label="Customers"
                />
                <SidebarNavLink
                  href="/dashboard/subscriptions"
                  icon={CreditCard}
                  label="Subscriptions"
                />
                <SidebarNavLink
                  href="/dashboard/activity-logs"
                  icon={Activity}
                  label="Activity Logs"
                />
                <SettingsSubmenu user={user} />
                {user?.permissions?.includes('view-users') && (
                  <SidebarNavLink
                    href="/dashboard/users"
                    icon={Users}
                    label="Users"
                  />
                )}
                {user?.permissions?.includes('view-roles') && (
                  <SidebarNavLink
                    href="/dashboard/roles"
                    icon={Shield}
                    label="Roles"
                  />
                )}
                {user?.permissions?.includes('view-permissions') && (
                  <SidebarNavLink
                    href="/dashboard/permissions"
                    icon={Key}
                    label="Permissions"
                  />
                )}
              </div>
            </Card>
          </aside>
          <section>{children}</section>
        </div>
      </main>
    </div>
    </TooltipProvider>
  )
}
