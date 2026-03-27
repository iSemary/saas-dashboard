"use client"

import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { LucideIcon, TrendingUp, TrendingDown } from "lucide-react"
import { cn } from "@/lib/utils"

interface KPICardProps {
  title: string
  value: string | number
  icon: LucideIcon
  trend?: {
    value: number
    label?: string
  }
  description?: string
  href?: string
}

export function KPICard({
  title,
  value,
  icon: Icon,
  trend,
  description,
  href,
}: KPICardProps) {
  const isPositive = trend ? trend.value >= 0 : undefined
  const TrendIcon = isPositive === true ? TrendingUp : isPositive === false ? TrendingDown : null

  const content = (
    <Card className={href ? "cursor-pointer hover:bg-muted/50 transition-colors" : ""}>
      <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
        <CardTitle className="text-sm font-medium">{title}</CardTitle>
        <Icon className="h-4 w-4 text-muted-foreground" />
      </CardHeader>
      <CardContent>
        <div className="text-2xl font-bold">{value}</div>
        {description && (
          <p className="text-xs text-muted-foreground mt-1">{description}</p>
        )}
        {trend && TrendIcon && (
          <div className={cn(
            "flex items-center gap-1 text-xs mt-2",
            isPositive ? "text-green-600 dark:text-green-400" : "text-red-600 dark:text-red-400"
          )}>
            <TrendIcon className="h-3 w-3" />
            <span>
              {Math.abs(trend.value)}% {trend.label || (isPositive ? "increase" : "decrease")}
            </span>
          </div>
        )}
      </CardContent>
    </Card>
  )

  if (href) {
    return (
      <a href={href} className="block">
        {content}
      </a>
    )
  }

  return content
}
