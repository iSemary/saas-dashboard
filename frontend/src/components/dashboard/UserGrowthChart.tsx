"use client"

import {
  Area,
  AreaChart,
  CartesianGrid,
  Legend,
  ResponsiveContainer,
  Tooltip,
  XAxis,
  YAxis,
} from "recharts"
import { ChartCard } from "./ChartCard"
import { useChartColorPalette } from "@/lib/chart-colors"

interface UserGrowthChartProps {
  data: Array<{
    date: string
    users: number
  }>
}

export function UserGrowthChart({ data }: UserGrowthChartProps) {
  const palette = useChartColorPalette()

  if (!data || data.length === 0) {
    return (
      <ChartCard title="User Growth" description="User growth over time">
        <div className="flex items-center justify-center h-[400px] text-muted-foreground">
          No user growth data available
        </div>
      </ChartCard>
    )
  }

  const formattedData = data.map((item) => ({
    ...item,
    date: new Date(item.date).toLocaleDateString("en-US", {
      month: "short",
      day: "numeric",
    }),
  }))

  return (
    <ChartCard title="User Growth" description="User growth over the last 12 months">
      <ResponsiveContainer width="100%" height={400}>
        <AreaChart data={formattedData} margin={{ top: 10, right: 30, left: 0, bottom: 0 }}>
          <defs>
            <linearGradient id="colorUsers" x1="0" y1="0" x2="0" y2="1">
              <stop offset="5%" stopColor={palette.chart2} stopOpacity={0.8} />
              <stop offset="95%" stopColor={palette.chart2} stopOpacity={0} />
            </linearGradient>
          </defs>
          <CartesianGrid strokeDasharray="3 3" className="stroke-muted" />
          <XAxis
            dataKey="date"
            className="text-xs"
            tick={{ fill: "hsl(var(--muted-foreground))" }}
          />
          <YAxis className="text-xs" tick={{ fill: "hsl(var(--muted-foreground))" }} />
          <Tooltip
            contentStyle={{
              backgroundColor: "hsl(var(--background))",
              border: "1px solid hsl(var(--border))",
              borderRadius: "6px",
            }}
          />
          <Legend />
          <Area
            type="monotone"
            dataKey="users"
            stroke={palette.chart2}
            fill="url(#colorUsers)"
          />
        </AreaChart>
      </ResponsiveContainer>
    </ChartCard>
  )
}
