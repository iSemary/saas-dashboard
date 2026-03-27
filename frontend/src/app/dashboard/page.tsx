"use client"

import { useState, useEffect } from "react"
import { getDashboardStats, type DashboardStatsResponse } from "@/lib/dashboard"
import { StatCard } from "@/components/dashboard/StatCard"
import { KPICard } from "@/components/dashboard/KPICard"
import { RevenueChart } from "@/components/dashboard/RevenueChart"
import { UserGrowthChart } from "@/components/dashboard/UserGrowthChart"
import { ActivityDistributionChart } from "@/components/dashboard/ActivityDistributionChart"
import { PerformanceMetricsChart } from "@/components/dashboard/PerformanceMetricsChart"
import {
  Users,
  UserCircle,
  CreditCard,
  Activity,
  DollarSign,
  TrendingUp,
} from "lucide-react"
import { toast } from "sonner"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"

export default function DashboardPage() {
  const [loading, setLoading] = useState(true)
  const [stats, setStats] = useState<DashboardStatsResponse["data"] | null>(null)

  useEffect(() => {
    loadStats()
  }, [])

  const loadStats = async () => {
    try {
      setLoading(true)
      const response = await getDashboardStats()
      setStats(response.data)
    } catch (error: any) {
      toast.error("Failed to load dashboard statistics")
      console.error(error)
    } finally {
      setLoading(false)
    }
  }

  if (loading) {
    return (
      <div className="space-y-6">
        <div>
          <h1 className="text-3xl font-bold">Dashboard</h1>
          <p className="text-muted-foreground">Loading dashboard statistics...</p>
        </div>
        <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
          {[1, 2, 3, 4].map((i) => (
            <div key={i} className="h-32 bg-muted animate-pulse rounded-lg" />
          ))}
        </div>
      </div>
    )
  }

  if (!stats) {
    return (
      <div className="space-y-6">
        <div>
          <h1 className="text-3xl font-bold">Dashboard</h1>
          <p className="text-muted-foreground">No data available</p>
        </div>
      </div>
    )
  }

  const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat("en-US", {
      style: "currency",
      currency: "USD",
      minimumFractionDigits: 0,
      maximumFractionDigits: 0,
    }).format(amount)
  }

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-3xl font-bold">Dashboard</h1>
        <p className="text-muted-foreground">
          Overview of your tenant administration
        </p>
      </div>

      {/* KPI Cards */}
      {stats.kpis && (
        <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
          {stats.kpis.revenue && (
            <KPICard
              title="Revenue"
              value={formatCurrency(stats.kpis.revenue.current)}
              icon={DollarSign}
              trend={{
                value: stats.kpis.revenue.growth,
                label: "vs last month",
              }}
            />
          )}
          {stats.kpis.users && (
            <KPICard
              title="Users"
              value={stats.kpis.users.current}
              icon={Users}
              trend={{
                value: stats.kpis.users.growth,
                label: "vs last month",
              }}
            />
          )}
          {stats.kpis.customers && (
            <KPICard
              title="Customers"
              value={stats.kpis.customers.current}
              icon={UserCircle}
              trend={{
                value: stats.kpis.customers.growth,
                label: "vs last month",
              }}
            />
          )}
          {stats.kpis.subscriptions && (
            <KPICard
              title="Subscriptions"
              value={stats.kpis.subscriptions.current}
              icon={CreditCard}
              trend={{
                value: stats.kpis.subscriptions.growth,
                label: "vs last month",
              }}
            />
          )}
        </div>
      )}

      {/* Stat Cards */}
      <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        {stats.stats.users && (
          <StatCard
            title="Total Users"
            value={stats.stats.users.total}
            icon={Users}
            href="/dashboard/users"
            description={stats.stats.users.active ? `${stats.stats.users.active} active` : undefined}
            trend={stats.stats.users.growth ? {
              value: Math.abs(stats.stats.users.growth),
              isPositive: stats.stats.users.growth >= 0,
            } : undefined}
          />
        )}
        {stats.stats.customers && (
          <StatCard
            title="Total Customers"
            value={stats.stats.customers.total}
            icon={UserCircle}
            href="/dashboard/customers"
          />
        )}
        {stats.stats.subscriptions && (
          <StatCard
            title="Active Subscriptions"
            value={stats.stats.subscriptions.active}
            icon={CreditCard}
            href="/dashboard/subscriptions"
            description={`${stats.stats.subscriptions.total} total`}
          />
        )}
        {stats.stats.activity_logs && (
          <StatCard
            title="Activity Logs"
            value={stats.stats.activity_logs.total}
            icon={Activity}
            href="/dashboard/activity-logs"
          />
        )}
      </div>

      {/* Charts Grid */}
      {stats.analytics && (
        <div className="grid gap-6 md:grid-cols-2">
          {stats.analytics.revenue_trend && stats.analytics.revenue_trend.length > 0 && (
            <RevenueChart data={stats.analytics.revenue_trend} />
          )}
          {stats.analytics.user_growth && stats.analytics.user_growth.length > 0 && (
            <UserGrowthChart data={stats.analytics.user_growth} />
          )}
        </div>
      )}

      {stats.analytics && (
        <div className="grid gap-6 md:grid-cols-2">
          {stats.analytics.activity_distribution && stats.analytics.activity_distribution.length > 0 && (
            <ActivityDistributionChart data={stats.analytics.activity_distribution} />
          )}
          {stats.analytics.performance_metrics && stats.analytics.performance_metrics.length > 0 && (
            <PerformanceMetricsChart data={stats.analytics.performance_metrics} />
          )}
        </div>
      )}

      {/* Recent Activity */}
      {stats.recent_activity && stats.recent_activity.length > 0 && (
        <Card>
          <CardHeader>
            <CardTitle>Recent Activity</CardTitle>
            <CardDescription>Latest activities in your tenant</CardDescription>
          </CardHeader>
          <CardContent>
            <div className="space-y-2">
              {stats.recent_activity.slice(0, 10).map((activity) => (
                <div
                  key={activity.id}
                  className="flex items-center justify-between p-3 border rounded-lg hover:bg-muted/50 transition-colors"
                >
                  <div>
                    <p className="text-sm font-medium">{activity.description}</p>
                    {activity.user && (
                      <p className="text-xs text-muted-foreground">
                        by {activity.user.name}
                      </p>
                    )}
                  </div>
                  <p className="text-xs text-muted-foreground">
                    {new Date(activity.created_at).toLocaleDateString()}
                  </p>
                </div>
              ))}
            </div>
          </CardContent>
        </Card>
      )}
    </div>
  )
}
