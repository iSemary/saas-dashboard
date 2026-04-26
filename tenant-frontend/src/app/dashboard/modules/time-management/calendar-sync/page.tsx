"use client";

import { useEffect, useState } from "react";
import { RefreshCw, Link2 } from "lucide-react";
import { ModulePageHeader } from "@/components/module-page-header";
import { toast } from "sonner";
import { getTmCalendarSyncStatus, connectTmCalendarProvider, disconnectTmCalendarProvider, triggerTmCalendarSync } from "@/lib/tm-resources";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";

type SyncStatus = { google_connected: boolean; outlook_connected: boolean; last_synced_at: string };

export default function CalendarSyncPage() {
  const [status, setStatus] = useState<SyncStatus | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    getTmCalendarSyncStatus()
      .then((s) => setStatus(s as SyncStatus))
      .catch(() => toast.error("Failed to load sync status"))
      .finally(() => setLoading(false));
  }, []);

  const handleConnect = async (provider: string) => {
    try { await connectTmCalendarProvider(provider); toast.success(`Connecting to ${provider}...`); }
    catch { toast.error(`Failed to connect ${provider}`); }
  };

  const handleDisconnect = async (provider: string) => {
    try { await disconnectTmCalendarProvider(provider); toast.success(`${provider} disconnected`); setStatus({ ...status!, [`${provider}_connected`]: false }); }
    catch { toast.error(`Failed to disconnect ${provider}`); }
  };

  const handleSync = async () => {
    try { await triggerTmCalendarSync(); toast.success("Sync triggered"); }
    catch { toast.error("Failed to trigger sync"); }
  };

  if (loading) {
    return <div className="flex min-h-[200px] items-center justify-center"><div className="size-6 animate-spin rounded-full border-2 border-primary border-t-transparent" /></div>;
  }

  return (
    <div className="space-y-4">
      <ModulePageHeader
        icon={Link2}
        titleKey="tm.calendar_sync"
        titleFallback="Calendar Sync"
        subtitleKey="tm.calendar_sync_subtitle"
        subtitleFallback="Sync with Google and Outlook calendars"
        dashboardHref="/dashboard/modules/time-management"
        moduleKey="time_management"
      />
      <div className="grid gap-4 sm:grid-cols-2 max-w-2xl">
        <Card>
          <CardHeader className="pb-2"><CardTitle className="text-sm font-medium">Google Calendar</CardTitle></CardHeader>
          <CardContent>
            {status?.google_connected ? (
              <Button variant="outline" size="sm" onClick={() => handleDisconnect("google")}>Disconnect</Button>
            ) : (
              <Button size="sm" onClick={() => handleConnect("google")}>Connect</Button>
            )}
          </CardContent>
        </Card>
        <Card>
          <CardHeader className="pb-2"><CardTitle className="text-sm font-medium">Outlook Calendar</CardTitle></CardHeader>
          <CardContent>
            {status?.outlook_connected ? (
              <Button variant="outline" size="sm" onClick={() => handleDisconnect("outlook")}>Disconnect</Button>
            ) : (
              <Button size="sm" onClick={() => handleConnect("outlook")}>Connect</Button>
            )}
          </CardContent>
        </Card>
      </div>
      <Button variant="outline" onClick={handleSync}>
        <RefreshCw className="mr-2 size-4" /> Trigger Sync
      </Button>
      {status?.last_synced_at && (
        <p className="text-xs text-muted-foreground">Last synced: {status.last_synced_at}</p>
      )}
    </div>
  );
}
