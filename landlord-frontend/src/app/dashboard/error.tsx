"use client";

import { useEffect } from "react";
import { ErrorDetails, ErrorActions } from "@/components/error/error-details";
import { ErrorBoundaryWrapper } from "@/components/error/error-boundary-wrapper";

interface DashboardErrorProps {
  error: Error & { digest?: string };
  reset: () => void;
}

export default function DashboardError({ error, reset }: DashboardErrorProps) {
  useEffect(() => {
    // Log error to console for debugging
    console.error("Dashboard Error:", error);
  }, [error]);

  return (
    <ErrorBoundaryWrapper>
      <div className="flex min-h-[400px] items-center justify-center p-4">
        <div className="w-full max-w-2xl">
          <ErrorDetails
            error={error}
            reset={reset}
            title="Dashboard Error"
            description="An error occurred while loading the dashboard content. Please try again."
          />
          <ErrorActions reset={reset} backHref="/dashboard" />
        </div>
      </div>
    </ErrorBoundaryWrapper>
  );
}
