"use client";

import { AlertCircle, Terminal, FileCode, Globe, Activity } from "lucide-react";
import { Badge } from "@/components/ui/badge";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { shouldShowErrorDetails, getAppEnv } from "@/lib/env";

interface ErrorDetailsProps {
  error: Error | null;
  reset?: () => void;
  statusCode?: number;
  requestUrl?: string;
  title?: string;
  description?: string;
}

export function ErrorDetails({
  error,
  statusCode,
  requestUrl,
  title = "An error occurred",
  description = "Something went wrong while processing your request.",
}: ErrorDetailsProps) {
  const showDetails = shouldShowErrorDetails();
  const appEnv = getAppEnv();

  if (!showDetails) {
    return (
      <Card className="w-full max-w-lg">
        <CardHeader className="text-center">
          <div className="mx-auto mb-4 flex size-16 items-center justify-center rounded-full bg-destructive/10">
            <AlertCircle className="size-8 text-destructive" />
          </div>
          <CardTitle className="text-2xl">{title}</CardTitle>
        </CardHeader>
        <CardContent className="space-y-4">
          <p className="text-center text-muted-foreground">{description}</p>
          <p className="text-center text-xs text-muted-foreground">
            Environment: {appEnv}
          </p>
        </CardContent>
      </Card>
    );
  }

  return (
    <Card className="w-full max-w-2xl">
      <CardHeader className="text-center">
        <div className="mx-auto mb-4 flex size-16 items-center justify-center rounded-full bg-destructive/10">
          <AlertCircle className="size-8 text-destructive" />
        </div>
        <CardTitle className="text-2xl">{title}</CardTitle>
        <div className="flex justify-center gap-2 mt-2">
          <Badge variant="outline" className="text-xs">
            <Activity className="mr-1 size-3" />
            {appEnv}
          </Badge>
          {statusCode && (
            <Badge variant="destructive" className="text-xs">
              {statusCode}
            </Badge>
          )}
        </div>
      </CardHeader>
      <CardContent className="space-y-4">
        <p className="text-center text-muted-foreground">{description}</p>

        {error?.message && (
          <div className="rounded-lg border border-destructive/20 bg-destructive/5 p-4">
            <div className="flex items-center gap-2 mb-2">
              <Terminal className="size-4 text-destructive" />
              <span className="text-sm font-semibold text-destructive">Error Message</span>
            </div>
            <code className="block text-sm break-all font-mono text-destructive">
              {error.message}
            </code>
          </div>
        )}

        {error?.stack && (
          <div className="rounded-lg border border-border bg-muted/50 p-4">
            <div className="flex items-center gap-2 mb-2">
              <FileCode className="size-4 text-muted-foreground" />
              <span className="text-sm font-semibold">Stack Trace</span>
            </div>
            <pre className="text-xs overflow-auto max-h-60 whitespace-pre-wrap font-mono text-muted-foreground">
              {error.stack}
            </pre>
          </div>
        )}

        {requestUrl && (
          <div className="rounded-lg border border-border bg-muted/50 p-4">
            <div className="flex items-center gap-2 mb-2">
              <Globe className="size-4 text-muted-foreground" />
              <span className="text-sm font-semibold">Request URL</span>
            </div>
            <code className="block text-sm break-all font-mono text-muted-foreground">
              {requestUrl}
            </code>
          </div>
        )}

        {error?.name && error.name !== "Error" && (
          <div className="flex justify-center">
            <Badge variant="secondary" className="text-xs">
              Error Type: {error.name}
            </Badge>
          </div>
        )}
      </CardContent>
    </Card>
  );
}

export function ErrorActions({
  reset,
  backHref = "/dashboard",
}: {
  reset?: () => void;
  backHref?: string;
}) {
  return (
    <div className="flex justify-center gap-3 mt-6">
      {reset && (
        <button
          onClick={reset}
          className="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2"
        >
          Try Again
        </button>
      )}
      <a
        href={backHref}
        className="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2"
      >
        Go to Dashboard
      </a>
    </div>
  );
}
