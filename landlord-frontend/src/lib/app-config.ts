/** Tab title / SEO — set `NEXT_PUBLIC_APP_TITLE` in `.env` */
export const APP_TITLE =
  process.env.NEXT_PUBLIC_APP_TITLE ?? "SaaS Landlord Dashboard";

/** Meta description — set `NEXT_PUBLIC_APP_DESCRIPTION` in `.env` */
export const APP_DESCRIPTION =
  process.env.NEXT_PUBLIC_APP_DESCRIPTION ?? "SaaS landlord administration dashboard";

/** Display name for headers, footers, and UI (defaults to `APP_TITLE`) */
export const APP_NAME = APP_TITLE;

/**
 * Public URL for the main app logo (file lives under `frontend/public/`).
 * Default: `public/assets/logo/logo.svg` — replace that file or set `NEXT_PUBLIC_APP_LOGO`.
 */
export const APP_LOGO_SRC =
  process.env.NEXT_PUBLIC_APP_LOGO ?? "/assets/logo/logo.svg";

/** Landlord organization subdomain — must match backend APP_LANDLORD_ORGANIZATION_NAME */
export const LANDLORD_SUBDOMAIN =
  process.env.NEXT_PUBLIC_LANDLORD_SUBDOMAIN ?? "landlord";
