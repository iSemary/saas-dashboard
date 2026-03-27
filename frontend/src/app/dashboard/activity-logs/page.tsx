"use client"

import { useState, useEffect } from "react"
import { getActivityLogs, exportActivityLogs, type ActivityLogFilters, type ActivityLog } from "@/lib/activity"
import { ActivityTimeline } from "@/components/activity/ActivityTimeline"
import { ActivityFilter } from "@/components/activity/ActivityFilter"
import { Button } from "@/components/ui/button"
import { toast } from "sonner"
import { ChevronLeft, ChevronRight, Loader2 } from "lucide-react"

export default function ActivityLogsPage() {
  const [loading, setLoading] = useState(true)
  const [logs, setLogs] = useState<ActivityLog[]>([])
  const [pagination, setPagination] = useState({
    current_page: 1,
    last_page: 1,
    per_page: 15,
    total: 0,
    from: null as number | null,
    to: null as number | null,
  })
  const [filters, setFilters] = useState<ActivityLogFilters>({
    page: 1,
    per_page: 15,
  })

  useEffect(() => {
    loadLogs()
  }, [filters])

  const loadLogs = async () => {
    try {
      setLoading(true)
      const response = await getActivityLogs(filters)
      setLogs(response.data)
      setPagination(response.pagination)
    } catch (error: any) {
      toast.error("Failed to load activity logs")
      console.error(error)
    } finally {
      setLoading(false)
    }
  }

  const handleExport = async () => {
    try {
      const blob = await exportActivityLogs(filters, "csv")
      const url = window.URL.createObjectURL(blob)
      const a = document.createElement("a")
      a.href = url
      a.download = `activity-logs-${new Date().toISOString()}.csv`
      document.body.appendChild(a)
      a.click()
      window.URL.revokeObjectURL(url)
      document.body.removeChild(a)
      toast.success("Activity logs exported successfully")
    } catch (error: any) {
      toast.error("Failed to export activity logs")
      console.error(error)
    }
  }

  const handlePageChange = (page: number) => {
    setFilters({ ...filters, page })
  }

  if (loading && logs.length === 0) {
    return (
      <div className="space-y-6">
        <div>
          <h1 className="text-3xl font-bold">Activity Logs</h1>
          <p className="text-muted-foreground">View system activity and audit logs</p>
        </div>
        <div className="flex items-center justify-center h-[400px]">
          <Loader2 className="h-8 w-8 animate-spin text-muted-foreground" />
        </div>
      </div>
    )
  }

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-3xl font-bold">Activity Logs</h1>
        <p className="text-muted-foreground">View system activity and audit logs</p>
      </div>

      <ActivityFilter
        filters={filters}
        onFiltersChange={setFilters}
        onExport={handleExport}
        totalCount={pagination.total}
      />

      {loading ? (
        <div className="flex items-center justify-center h-[400px]">
          <Loader2 className="h-8 w-8 animate-spin text-muted-foreground" />
        </div>
      ) : logs.length > 0 ? (
        <>
          <ActivityTimeline logs={logs} />

          {/* Pagination */}
          {pagination.last_page > 1 && (
            <div className="flex items-center justify-between">
              <div className="text-sm text-muted-foreground">
                Showing {pagination.from} to {pagination.to} of {pagination.total} results
              </div>
              <div className="flex items-center gap-2">
                <Button
                  variant="outline"
                  size="sm"
                  onClick={() => handlePageChange(pagination.current_page - 1)}
                  disabled={pagination.current_page === 1}
                >
                  <ChevronLeft className="h-4 w-4" />
                  Previous
                </Button>
                <div className="text-sm">
                  Page {pagination.current_page} of {pagination.last_page}
                </div>
                <Button
                  variant="outline"
                  size="sm"
                  onClick={() => handlePageChange(pagination.current_page + 1)}
                  disabled={pagination.current_page === pagination.last_page}
                >
                  Next
                  <ChevronRight className="h-4 w-4" />
                </Button>
              </div>
            </div>
          )}
        </>
      ) : (
        <div className="text-center py-12 text-muted-foreground">
          No activity logs found
        </div>
      )}
    </div>
  )
}
