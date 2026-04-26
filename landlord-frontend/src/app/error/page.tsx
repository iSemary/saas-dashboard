"use client";

import { AlertCircle, Home, ArrowLeft } from "lucide-react";
import Link from "next/link";
import { useRouter } from "next/navigation";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";

interface ErrorPageProps {
  title: string;
  description: string;
  errorDetails?: string;
  showBackButton?: boolean;
  backHref?: string;
  showHomeButton?: boolean;
}

export default function ErrorPage({
  title,
  description,
  errorDetails,
  showBackButton = true,
  backHref,
  showHomeButton = true,
}: ErrorPageProps) {
  const router = useRouter();

  const handleBack = () => {
    if (backHref) {
      router.push(backHref);
    } else {
      router.back();
    }
  };

  return (
    <div className="flex min-h-[400px] items-center justify-center p-4">
      <Card className="w-full max-w-lg">
        <CardHeader className="text-center">
          <div className="mx-auto mb-4 flex size-16 items-center justify-center rounded-full bg-destructive/10">
            <AlertCircle className="size-8 text-destructive" />
          </div>
          <CardTitle className="text-2xl">{title}</CardTitle>
        </CardHeader>
        <CardContent className="space-y-6">
          <p className="text-center text-muted-foreground">{description}</p>
          
          {errorDetails && (
            <div className="rounded-lg border border-border bg-muted/50 p-4">
              <Badge variant="outline" className="mb-2 text-xs">
                Error Details
              </Badge>
              <p className="text-sm text-muted-foreground">{errorDetails}</p>
            </div>
          )}

          <div className="flex justify-center gap-3">
            {showBackButton && (
              <Button variant="outline" onClick={handleBack}>
                <ArrowLeft className="mr-2 size-4" />
                Go Back
              </Button>
            )}
            {showHomeButton && (
              <Link href="/dashboard">
                <Button>
                  <Home className="mr-2 size-4" />
                  Dashboard
                </Button>
              </Link>
            )}
          </div>
        </CardContent>
      </Card>
    </div>
  );
}
