/**
 * Lets the axios 401 interceptor trigger a Next.js client navigation instead of
 * `window.location.href`, which causes a full reload and feels like the page "refreshing".
 */
let redirectToLoginFn: (() => void) | null = null;

export function setAuthRedirectToLogin(fn: (() => void) | null): void {
  redirectToLoginFn = fn;
}

export function redirectToLoginSPA(): void {
  if (redirectToLoginFn) {
    redirectToLoginFn();
    return;
  }
  if (typeof window !== "undefined") {
    window.location.href = "/login";
  }
}
