"use client"

import { useEffect, useState } from "react"

/**
 * Get chart colors that adapt to theme
 */
export function useChartColors() {
  const [colors, setColors] = useState<string[]>([
    "hsl(var(--chart-1))",
    "hsl(var(--chart-2))",
    "hsl(var(--chart-3))",
    "hsl(var(--chart-4))",
    "hsl(var(--chart-5))",
  ])

  useEffect(() => {
    // Colors are CSS variables, so they automatically adapt to theme
    setColors([
      "hsl(var(--chart-1))",
      "hsl(var(--chart-2))",
      "hsl(var(--chart-3))",
      "hsl(var(--chart-4))",
      "hsl(var(--chart-5))",
    ])
  }, [])

  return colors
}

/**
 * Get chart color palette as an object
 */
export function useChartColorPalette() {
  const colors = useChartColors()
  
  return {
    chart1: colors[0] || "hsl(var(--chart-1))",
    chart2: colors[1] || "hsl(var(--chart-2))",
    chart3: colors[2] || "hsl(var(--chart-3))",
    chart4: colors[3] || "hsl(var(--chart-4))",
    chart5: colors[4] || "hsl(var(--chart-5))",
    get: (index: number) => {
      const safeIndex = Math.max(1, Math.min(5, index)) - 1
      return colors[safeIndex] || `hsl(var(--chart-${index}))`
    },
  }
}
