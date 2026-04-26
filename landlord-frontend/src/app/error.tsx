"use client";

import { useEffect } from "react";
import { ErrorDetails, ErrorActions } from "@/components/error/error-details";

interface GlobalErrorProps {
  error: Error & { digest?: string };
  reset: () => void;
}

export default function GlobalError({ error, reset }: GlobalErrorProps) {
  useEffect(() => {
    // Log error to console in development
    console.error("Global Error:", error);
  }, [error]);

  return (
    <html>
      <body className="min-h-screen flex items-center justify-center p-4 bg-background">
        <div className="w-full max-w-2xl">
          <ErrorDetails
            error={error}
            reset={reset}
            title="Application Error"
            description="A critical error occurred in the application. Please try again or contact support if the problem persists."
          />
          <ErrorActions reset={reset} backHref="/dashboard" />
        </div>
      </body>
    </html>
  );
}
