"use client";

import { Card, CardContent, CardHeader } from "@/components/ui/card";
import { Skeleton } from "@/components/ui/skeleton";

export function DashboardAuthShell() {
  return (
    <div className="flex min-h-svh w-full flex-col bg-background">
      <header className="flex h-14 shrink-0 items-center gap-3 border-b border-border px-4">
        <Skeleton className="h-9 w-9 rounded-lg" />
        <Skeleton className="h-5 w-40 rounded-md" />
        <div className="ml-auto flex gap-2">
          <Skeleton className="h-9 w-24 rounded-md" />
          <Skeleton className="h-9 w-9 rounded-md" />
        </div>
      </header>
      <div className="flex flex-1 gap-4 p-4">
        <aside className="hidden w-56 shrink-0 space-y-2 md:block">
          {Array.from({ length: 8 }).map((_, i) => (
            <Skeleton key={i} className="h-9 w-full rounded-lg" />
          ))}
        </aside>
        <main className="flex flex-1 flex-col gap-4">
          <Card className="flex-1 border-border/80">
            <CardHeader className="space-y-2 pb-2">
              <Skeleton className="h-7 w-48 rounded-md" />
              <Skeleton className="h-4 w-full max-w-md rounded-md" />
            </CardHeader>
            <CardContent className="space-y-3">
              <Skeleton className="h-32 w-full rounded-xl" />
              <Skeleton className="h-24 w-full rounded-xl" />
            </CardContent>
          </Card>
        </main>
      </div>
    </div>
  );
}
