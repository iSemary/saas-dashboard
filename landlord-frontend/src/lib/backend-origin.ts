/** Laravel app origin (no `/api/...`), used for `/storage/...` URLs. */
export function getBackendOrigin(): string {
  const explicit = process.env.NEXT_PUBLIC_BACKEND_ORIGIN?.trim();
  if (explicit) return explicit.replace(/\/$/, "");

  const api = process.env.NEXT_PUBLIC_API_BASE_URL ?? "";
  try {
    const u = new URL(api);
    return `${u.protocol}//${u.host}`;
  } catch {
    return "http://127.0.0.1:8000";
  }
}

/** Build absolute URL for a path returned by Laravel `Storage::disk('public')` (e.g. `branding/1/logo.png`). */
export function storageUrlFromPath(path: string | null | undefined): string | null {
  if (!path?.trim()) return null;
  const clean = path.replace(/^\/+/, "");
  return `${getBackendOrigin()}/storage/${clean}`;
}
