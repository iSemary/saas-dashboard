"use client";

import { useEffect, useState } from "react";
import { UserCheck, LogIn, LogOut } from "lucide-react";
import { ModulePageHeader } from "@/components/module-page-header";
import { toast } from "sonner";
import { listTmAttendance, clockInTm, clockOutTm } from "@/lib/tm-resources";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";

type AttendanceRecord = { id: number; clock_in: string; clock_out: string; duration: number; status: string };

export default function AttendancePage() {
  const [records, setRecords] = useState<AttendanceRecord[]>([]);
  const [loading, setLoading] = useState(true);
  const [actionLoading, setActionLoading] = useState(false);

  const loadRecords = () => {
    listTmAttendance<AttendanceRecord>()
      .then((r) => setRecords(r.data ?? []))
      .catch(() => toast.error("Failed to load attendance"))
      .finally(() => setLoading(false));
  };

  useEffect(() => { loadRecords(); }, []);

  const handleClockIn = async () => {
    setActionLoading(true);
    try { await clockInTm(); toast.success("Clocked in"); loadRecords(); }
    catch { toast.error("Failed to clock in"); }
    finally { setActionLoading(false); }
  };

  const handleClockOut = async () => {
    setActionLoading(true);
    try { await clockOutTm(); toast.success("Clocked out"); loadRecords(); }
    catch { toast.error("Failed to clock out"); }
    finally { setActionLoading(false); }
  };

  if (loading) {
    return <div className="flex min-h-[200px] items-center justify-center"><div className="size-6 animate-spin rounded-full border-2 border-primary border-t-transparent" /></div>;
  }

  return (
    <div className="space-y-4">
      <ModulePageHeader
        icon={UserCheck}
        titleKey="tm.attendance"
        titleFallback="Attendance"
        subtitleKey="tm.attendance_subtitle"
        subtitleFallback="Track your attendance with clock in/out"
        dashboardHref="/dashboard/modules/time-management"
        moduleKey="time_management"
      />
      <div className="flex gap-3">
        <Button onClick={handleClockIn} disabled={actionLoading}>
          <LogIn className="mr-2 size-4" /> Clock In
        </Button>
        <Button variant="outline" onClick={handleClockOut} disabled={actionLoading}>
          <LogOut className="mr-2 size-4" /> Clock Out
        </Button>
      </div>
      <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        {records.map((r) => (
          <Card key={r.id}>
            <CardHeader className="pb-2">
              <CardTitle className="text-sm font-medium">{r.clock_in}</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="text-xs text-muted-foreground">Out: {r.clock_out ?? "—"}</div>
              <div className="text-xs text-muted-foreground">Duration: {r.duration} min · {r.status}</div>
            </CardContent>
          </Card>
        ))}
        {records.length === 0 && (
          <div className="col-span-full text-center text-muted-foreground py-8">No attendance records found.</div>
        )}
      </div>
    </div>
  );
}
