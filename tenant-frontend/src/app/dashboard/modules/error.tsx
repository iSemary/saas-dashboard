"use client";

import { useEffect } from "react";
import { usePathname } from "next/navigation";
import { Lock, Home, ArrowLeft } from "lucide-react";
import Link from "next/link";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { ErrorDetails, ErrorActions } from "@/components/error/error-details";
import { shouldShowErrorDetails, getAppEnv } from "@/lib/env";

interface ModuleErrorProps {
  error: Error & { digest?: string };
  reset: () => void;
}

export default function ModuleError({ error, reset }: ModuleErrorProps) {
  const pathname = usePathname();
  const showDetails = shouldShowErrorDetails();
  const appEnv = getAppEnv();

  useEffect(() => {
    console.error("Module Error:", error);
  }, [error]);

  // Check if this is a module access/subscription error
  const isModuleAccessError =
    error.message?.toLowerCase().includes("module") ||
    error.message?.toLowerCase().includes("subscription") ||
    error.message?.toLowerCase().includes("unauthorized") ||
    error.message?.toLowerCase().includes("access");

  if (isModuleAccessError) {
    return (
      <div className="flex min-h-[400px] items-center justify-center p-4">
        <Card className="w-full max-w-lg">
          <CardHeader className="text-center">
            <div className="mx-auto mb-4 flex size-16 items-center justify-center rounded-full bg-destructive/10">
              <Lock className="size-8 text-destructive" />
            </div>
            <CardTitle className="text-2xl">Module Access Error</CardTitle>
            {showDetails && (
              <div className="flex justify-center mt-2">
                <Badge variant="outline" className="text-xs">
                  {appEnv}
                </Badge>
              </div>
            )}
          </CardHeader>
          <CardContent className="space-y-6">
            <p className="text-center text-muted-foreground">
              You do not have access to this module. Please contact your administrator
              or subscribe to access this feature.
            </p>

            {showDetails && pathname && (
              <div className="rounded-lg border border-border bg-muted/50 p-4">
                <Badge variant="outline" className="mb-2 text-xs">
                  Requested Path
                </Badge>
                <code className="block text-sm break-all font-mono text-muted-foreground">
                  {pathname}
                </code>
              </div>
            )}

            {showDetails && error?.message && (
              <div className="rounded-lg border border-destructive/20 bg-destructive/5 p-4">
                <Badge variant="destructive" className="mb-2 text-xs">
                  Error Details
                </Badge>
                <p className="text-sm text-destructive">{error.message}</p>
              </div>
            )}

            <div className="flex justify-center gap-3">
              <Link href="/dashboard">
                <Button variant="outline">
                  <ArrowLeft className="mr-2 size-4" />
                  Back
                </Button>
              </Link>
              <Link href="/dashboard">
                <Button>
                  <Home className="mr-2 size-4" />
                  Dashboard
                </Button>
              </Link>
            </div>
          </CardContent>
        </Card>
      </div>
    );
  }

  // Generic error fallback
  return (
    <div className="flex min-h-[400px] items-center justify-center p-4">
      <div className="w-full max-w-2xl">
        <ErrorDetails
          error={error}
          reset={reset}
          title="Module Error"
          description="An error occurred while loading the module. Please try again."
        />
        <ErrorActions reset={reset} backHref="/dashboard" />
      </div>
    </div>
  );
}
