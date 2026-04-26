"use client";

import { usePathname } from "next/navigation";
import { FileQuestion, Home, ArrowLeft } from "lucide-react";
import Link from "next/link";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { shouldShowErrorDetails, getAppEnv } from "@/lib/env";

export default function NotFound() {
  const pathname = usePathname();
  const showDetails = shouldShowErrorDetails();
  const appEnv = getAppEnv();

  return (
    <div className="min-h-screen flex items-center justify-center p-4 bg-background">
      <Card className="w-full max-w-lg">
        <CardHeader className="text-center">
          <div className="mx-auto mb-4 flex size-16 items-center justify-center rounded-full bg-muted">
            <FileQuestion className="size-8 text-muted-foreground" />
          </div>
          <CardTitle className="text-2xl">Page Not Found</CardTitle>
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
            The page you are looking for does not exist or has been moved.
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
