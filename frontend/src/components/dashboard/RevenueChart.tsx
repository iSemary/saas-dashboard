"use client"

import {
  Line,
  LineChart,
  CartesianGrid,
  Legend,
  ResponsiveContainer,
  Tooltip,
  XAxis,
  YAxis,
} from "recharts"
import { ChartCard } from "./ChartCard"
import { useChartColorPalette } from "@/lib/chart-colors"

interface RevenueChartProps {
  data: Array<{
    date: string
    revenue: number
  }>
}

export function RevenueChart({ data }: RevenueChartProps) {
  const palette = useChartColorPalette()

  if (!data || data.length === 0) {
    return (
      <ChartCard title="Revenue Trends" description="Revenue over time">
        <div className="flex items-center justify-center h-[400px] text-muted-foreground">
          No revenue data available
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
    <ChartCard title="Revenue Trends" description="Revenue over the last 12 months">
      <ResponsiveContainer width="100%" height={400}>
        <LineChart data={formattedData} margin={{ top: 10, right: 30, left: 0, bottom: 0 }}>
          <CartesianGrid strokeDasharray="3 3" className="stroke-muted" />
          <XAxis
            dataKey="date"
            className="text-xs"
            tick={{ fill: "hsl(var(--muted-foreground))" }}
          />
          <YAxis
            className="text-xs"
            tick={{ fill: "hsl(var(--muted-foreground))" }}
            tickFormatter={(value) => `$${value.toLocaleString()}`}
          />
          <Tooltip
            contentStyle={{
              backgroundColor: "hsl(var(--background))",
              border: "1px solid hsl(var(--border))",
              borderRadius: "6px",
            }}
            formatter={(value: number) => `$${value.toLocaleString()}`}
          />
          <Legend />
          <Line
            type="monotone"
            dataKey="revenue"
            stroke={palette.chart1}
            strokeWidth={2}
            dot={{ fill: palette.chart1, r: 4 }}
            activeDot={{ r: 6 }}
          />
        </LineChart>
      </ResponsiveContainer>
    </ChartCard>
  )
}
