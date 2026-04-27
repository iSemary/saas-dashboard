"use client";

import { useEffect } from "react";
import { skipNextLoginRedirect } from "@/lib/api";

interface ErrorBoundaryWrapperProps {
  children: React.ReactNode;
}

/**
 * ErrorBoundaryWrapper prevents API 401 errors from triggering login redirects
 * when an error boundary is active. This ensures errors are displayed on the
 * error page instead of redirecting to login.
 */
export function ErrorBoundaryWrapper({ children }: ErrorBoundaryWrapperProps) {
  useEffect(() => {
    // When this wrapper mounts (meaning an error boundary caught an error),
    // tell the API to skip the next login redirect
    skipNextLoginRedirect();
  }, []);

  return <>{children}</>;
}
