"use client"

import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Search, Filter, Download } from "lucide-react"
import { type ActivityLogFilters } from "@/lib/activity"

interface ActivityFilterProps {
  filters: ActivityLogFilters
  onFiltersChange: (filters: ActivityLogFilters) => void
  onExport: () => void
  totalCount: number
}

export function ActivityFilter({
  filters,
  onFiltersChange,
  onExport,
  totalCount,
}: ActivityFilterProps) {
  const updateFilter = (key: keyof ActivityLogFilters, value: any) => {
    onFiltersChange({ ...filters, [key]: value, page: 1 })
  }

  return (
    <Card>
      <CardHeader>
        <div className="flex items-center justify-between">
          <div>
            <CardTitle>Activity Logs</CardTitle>
            <CardDescription>
              {totalCount} total activity log{totalCount !== 1 ? "s" : ""}
            </CardDescription>
          </div>
          <Button onClick={onExport} variant="outline" size="sm">
            <Download className="h-4 w-4 mr-2" />
            Export
          </Button>
        </div>
      </CardHeader>
      <CardContent>
        <div className="grid gap-4 md:grid-cols-4">
          <div className="space-y-2">
            <Label htmlFor="search">Search</Label>
            <div className="relative">
              <Search className="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
              <Input
                id="search"
                placeholder="Search activities..."
                value={filters.search || ""}
                onChange={(e) => updateFilter("search", e.target.value)}
                className="pl-8"
              />
            </div>
          </div>
          <div className="space-y-2">
            <Label htmlFor="event_type">Event Type</Label>
            <Select
              value={filters.event_type || ""}
              onValueChange={(value) => updateFilter("event_type", value || undefined)}
            >
              <SelectTrigger id="event_type">
                <SelectValue placeholder="All events" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="">All events</SelectItem>
                <SelectItem value="created">Created</SelectItem>
                <SelectItem value="updated">Updated</SelectItem>
                <SelectItem value="deleted">Deleted</SelectItem>
                <SelectItem value="restored">Restored</SelectItem>
              </SelectContent>
            </Select>
          </div>
          <div className="space-y-2">
            <Label htmlFor="date_from">From Date</Label>
            <Input
              id="date_from"
              type="date"
              value={filters.date_from || ""}
              onChange={(e) => updateFilter("date_from", e.target.value || undefined)}
            />
          </div>
          <div className="space-y-2">
            <Label htmlFor="date_to">To Date</Label>
            <Input
              id="date_to"
              type="date"
              value={filters.date_to || ""}
              onChange={(e) => updateFilter("date_to", e.target.value || undefined)}
            />
          </div>
        </div>
        {(filters.search || filters.event_type || filters.date_from || filters.date_to) && (
          <div className="mt-4 flex items-center gap-2">
            <Button
              variant="ghost"
              size="sm"
              onClick={() => onFiltersChange({ page: 1, per_page: filters.per_page })}
            >
              Clear filters
            </Button>
          </div>
        )}
      </CardContent>
    </Card>
  )
}
