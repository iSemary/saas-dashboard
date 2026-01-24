"use client"

import { useState, useEffect } from "react"
import { getDashboardStats, type DashboardStatsResponse } from "@/lib/dashboard"
import { StatCard } from "@/components/dashboard/StatCard"
import {
  Users,
  UserCircle,
  CreditCard,
  Activity,
} from "lucide-react"
import { toast } from "sonner"

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

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-3xl font-bold">Dashboard</h1>
        <p className="text-muted-foreground">
          Overview of your tenant administration
        </p>
      </div>

      {/* Stat Cards */}
      <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        {stats.stats.users && (
          <StatCard
            title="Total Users"
            value={stats.stats.users.total}
            icon={Users}
            href="/dashboard/users"
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

      {/* Recent Activity */}
      {stats.recent_activity && stats.recent_activity.length > 0 && (
        <div className="space-y-4">
          <h2 className="text-xl font-semibold">Recent Activity</h2>
          <div className="space-y-2">
            {stats.recent_activity.slice(0, 10).map((activity) => (
              <div
                key={activity.id}
                className="flex items-center justify-between p-3 border rounded-lg"
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
        </div>
      )}
    </div>
  )
}
