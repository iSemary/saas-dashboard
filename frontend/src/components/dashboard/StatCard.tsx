"use client"

import Link from "next/link"
import { Card, CardContent, CardHeader } from "@/components/ui/card"
import { LucideIcon } from "lucide-react"

interface StatCardProps {
  title: string
  value: string | number
  icon?: LucideIcon
  href?: string
  description?: string
  trend?: {
    value: number
    isPositive: boolean
  }
}

export function StatCard({
  title,
  value,
  icon: Icon,
  href,
  description,
  trend,
}: StatCardProps) {
  // Format title: if more than 2 words, split into first 2 words and the rest
  const formatTitle = (titleText: string) => {
    const words = titleText.split(" ")
    if (words.length > 2) {
      const firstTwo = words.slice(0, 2).join(" ")
      const rest = words.slice(2).join(" ")
      return { firstLine: firstTwo, secondLine: rest }
    }
    return { firstLine: titleText, secondLine: null }
  }

  const formattedTitle = formatTitle(title)

  const content = (
    <Card className={`h-full flex flex-col ${href ? "cursor-pointer hover:bg-muted/50 transition-colors" : ""}`}>
      <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
        <div className="flex-1">
          <p className="text-sm font-medium text-muted-foreground">
            {formattedTitle.secondLine ? (
              <>
                {formattedTitle.firstLine}
                <br />
                {formattedTitle.secondLine}
              </>
            ) : (
              formattedTitle.firstLine
            )}
          </p>
        </div>
        {Icon && (
          <div className="h-4 w-4 text-muted-foreground">
            <Icon className="h-4 w-4" />
          </div>
        )}
      </CardHeader>
      <CardContent className="flex-1 flex flex-col">
        <div className="text-2xl font-bold">{value}</div>
        {description && (
          <p className="text-xs text-muted-foreground mt-1">{description}</p>
        )}
        {trend && (
          <p
            className={`text-xs mt-1 ${
              trend.isPositive ? "text-green-600" : "text-red-600"
            }`}
          >
            {trend.isPositive ? "↑" : "↓"} {Math.abs(trend.value)}%
          </p>
        )}
      </CardContent>
    </Card>
  )

  if (href) {
    return (
      <Link href={href} className="h-full block">
        {content}
      </Link>
    )
  }

  return content
}
