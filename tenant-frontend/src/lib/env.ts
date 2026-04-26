/**
 * Environment detection utilities
 */

/**
 * Check if the app is running in local development environment
 */
export function isLocal(): boolean {
  return process.env.NEXT_PUBLIC_APP_ENV === "local" || process.env.NODE_ENV === "development";
}

/**
 * Check if the app is running in testing environment
 */
export function isTesting(): boolean {
  return process.env.NEXT_PUBLIC_APP_ENV === "testing" || process.env.NODE_ENV === "test";
}

/**
 * Check if detailed error information should be shown
 * (local or testing environments)
 */
export function shouldShowErrorDetails(): boolean {
  return isLocal() || isTesting();
}

/**
 * Get the current app environment
 */
export function getAppEnv(): string {
  return process.env.NEXT_PUBLIC_APP_ENV || process.env.NODE_ENV || "production";
}
