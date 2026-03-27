"use client"

import { Cell, Legend, Pie, PieChart, ResponsiveContainer, Tooltip } from "recharts"
import { ChartCard } from "./ChartCard"
import { useChartColors } from "@/lib/chart-colors"

interface ActivityDistributionChartProps {
  data: Array<{
    type: string
    count: number
  }>
}

export function ActivityDistributionChart({ data }: ActivityDistributionChartProps) {
  const colors = useChartColors()

  if (!data || data.length === 0) {
    return (
      <ChartCard title="Activity Distribution" description="Distribution of activities by type">
        <div className="flex items-center justify-center h-[400px] text-muted-foreground">
          No activity data available
        </div>
      </ChartCard>
    )
  }

  const total = data.reduce((sum, item) => sum + item.count, 0)
  const chartData = data.map((item) => ({
    name: item.type,
    value: item.count,
    percentage: ((item.count / total) * 100).toFixed(1),
  }))

  const renderLabel = (entry: { name: string; value: number; percentage: string }) => {
    return `${entry.name}: ${entry.value} (${entry.percentage}%)`
  }

  return (
    <ChartCard title="Activity Distribution" description="Distribution of activities by type">
      <ResponsiveContainer width="100%" height={400}>
        <PieChart>
          <Pie
            data={chartData}
            cx="50%"
            cy="50%"
            labelLine={false}
            label={renderLabel}
            outerRadius={120}
            fill="#8884d8"
            dataKey="value"
          >
            {chartData.map((entry, index) => (
              <Cell key={`cell-${index}`} fill={colors[index % colors.length]} />
            ))}
          </Pie>
          <Tooltip
            contentStyle={{
              backgroundColor: "hsl(var(--background))",
              border: "1px solid hsl(var(--border))",
              borderRadius: "6px",
            }}
          />
          <Legend />
        </PieChart>
      </ResponsiveContainer>
    </ChartCard>
  )
}
