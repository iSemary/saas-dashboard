"use client"

import {
  Bar,
  BarChart,
  CartesianGrid,
  Legend,
  ResponsiveContainer,
  Tooltip,
  XAxis,
  YAxis,
} from "recharts"
import { ChartCard } from "./ChartCard"
import { useChartColorPalette } from "@/lib/chart-colors"

interface PerformanceMetricsChartProps {
  data: Array<{
    metric: string
    value: number
  }>
}

export function PerformanceMetricsChart({ data }: PerformanceMetricsChartProps) {
  const palette = useChartColorPalette()

  if (!data || data.length === 0) {
    return (
      <ChartCard title="Performance Metrics" description="Key performance indicators">
        <div className="flex items-center justify-center h-[400px] text-muted-foreground">
          No performance data available
        </div>
      </ChartCard>
    )
  }

  const formattedData = data.map((item) => ({
    name: item.metric,
    value: item.value,
  }))

  return (
    <ChartCard title="Performance Metrics" description="Key performance indicators">
      <ResponsiveContainer width="100%" height={400}>
        <BarChart data={formattedData} margin={{ top: 10, right: 30, left: 0, bottom: 0 }}>
          <CartesianGrid strokeDasharray="3 3" className="stroke-muted" />
          <XAxis
            dataKey="name"
            className="text-xs"
            tick={{ fill: "hsl(var(--muted-foreground))" }}
            angle={-45}
            textAnchor="end"
            height={80}
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
          <Bar dataKey="value" fill={palette.chart3} radius={[4, 4, 0, 0]} />
        </BarChart>
      </ResponsiveContainer>
    </ChartCard>
  )
}
