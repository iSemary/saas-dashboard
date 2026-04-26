"use client";

import { useEffect, useState } from "react";
import { Timer, Play, Square } from "lucide-react";
import { ModulePageHeader } from "@/components/module-page-header";
import { toast } from "sonner";
import { getTmActiveSession, startTmSession, stopTmSession } from "@/lib/tm-resources";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";

type Session = { id: string; description: string; started_at: string; elapsed?: number };

export default function TimerPage() {
  const [session, setSession] = useState<Session | null>(null);
  const [loading, setLoading] = useState(true);
  const [starting, setStarting] = useState(false);
  const [stopping, setStopping] = useState(false);

  useEffect(() => {
    getTmActiveSession()
      .then((s) => setSession(s as Session | null))
      .catch(() => {})
      .finally(() => setLoading(false));
  }, []);

  const handleStart = async () => {
    setStarting(true);
    try {
      const s = await startTmSession({ description: "Timer session" });
      setSession(s as Session);
      toast.success("Timer started");
    } catch {
      toast.error("Failed to start timer");
    } finally {
      setStarting(false);
    }
  };

  const handleStop = async () => {
    if (!session?.id) return;
    setStopping(true);
    try {
      await stopTmSession(session.id);
      setSession(null);
      toast.success("Timer stopped");
    } catch {
      toast.error("Failed to stop timer");
    } finally {
      setStopping(false);
    }
  };

  if (loading) {
    return <div className="flex min-h-[200px] items-center justify-center"><div className="size-6 animate-spin rounded-full border-2 border-primary border-t-transparent" /></div>;
  }

  return (
    <div className="space-y-4">
      <ModulePageHeader
        icon={Timer}
        titleKey="tm.timer"
        titleFallback="My Timer"
        subtitleKey="tm.timer_subtitle"
        subtitleFallback="Track your time with a simple timer"
        dashboardHref="/dashboard/modules/time-management"
        moduleKey="time_management"
      />
      <Card className="max-w-md mx-auto">
        <CardHeader className="text-center">
          <CardTitle>{session ? "Timer Running" : "Timer Idle"}</CardTitle>
        </CardHeader>
        <CardContent className="flex flex-col items-center gap-4">
          {session ? (
            <>
              <div className="text-4xl font-mono font-bold">
                {session.elapsed != null ? `${Math.floor(session.elapsed / 60)}m ${session.elapsed % 60}s` : "Running..."}
              </div>
              <p className="text-sm text-muted-foreground">{session.description}</p>
              <Button variant="destructive" onClick={handleStop} disabled={stopping}>
                <Square className="mr-2 size-4" /> {stopping ? "Stopping..." : "Stop Timer"}
              </Button>
            </>
          ) : (
            <Button onClick={handleStart} disabled={starting}>
              <Play className="mr-2 size-4" /> {starting ? "Starting..." : "Start Timer"}
            </Button>
          )}
        </CardContent>
      </Card>
    </div>
  );
}
