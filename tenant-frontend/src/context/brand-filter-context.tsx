"use client";

import {
  createContext,
  useCallback,
  useContext,
  useEffect,
  useMemo,
  useState,
  type ReactNode,
} from "react";
import { useParams, usePathname, useRouter } from "next/navigation";
import { useModule } from "@/context/module-context";

export type BrandFilter = {
  slug: string;
  name: string;
  id: number;
} | null;

type BrandFilterContextValue = {
  brandFilter: BrandFilter;
  setBrandFilter: (filter: BrandFilter) => void;
  clearBrandFilter: () => void;
  isBrandFiltered: boolean;
  getBrandScopedPath: (path: string) => string;
  brandError: string | null;
};

const BrandFilterContext = createContext<BrandFilterContextValue | null>(null);

export function BrandFilterProvider({ children }: { children: ReactNode }) {
  const router = useRouter();
  const params = useParams();
  const pathname = usePathname();
  const { subscribedModules } = useModule();
  const [brandError, setBrandError] = useState<string | null>(null);

  // Extract brand slug and module key from URL
  // First try dynamic route params, then fall back to parsing pathname
  // Expected path: /dashboard/modules/[module]/[brand]/... or /dashboard/modules/[module]/...
  const pathParts = pathname?.split('/').filter(Boolean) ?? [];
  const moduleKeyFromPath = pathParts[2] === 'modules' ? pathParts[3] : undefined;
  const potentialBrandSlug = pathParts[2] === 'modules' ? pathParts[4] : undefined;

  // Reserved slugs that cannot be brand names (sub-page names)
  const reservedSlugs = ['new', 'create', 'edit', 'show', 'crm', 'hr', 'pos', 'survey', 'inventory', 'sales', 'accounting', 'email-marketing', 'expenses', 'project-management', 'sms-marketing', 'time-management'];
  const isBrandSlug = potentialBrandSlug && !reservedSlugs.includes(potentialBrandSlug);
  const brandSlugFromPath = isBrandSlug ? potentialBrandSlug : undefined;

  // Extract brand slug from URL params (e.g., /dashboard/modules/crm/acme-corp)
  const brandSlugFromUrl = (params?.brand as string | undefined) ?? brandSlugFromPath;
  const moduleKeyFromUrl = (params?.module as string | undefined) ?? moduleKeyFromPath;

  // Resolve brand details from subscribed modules
  const brandFilter: BrandFilter = useMemo(() => {
    if (!brandSlugFromUrl || !moduleKeyFromUrl) return null;

    // Skip reserved path segments that are not brand slugs
    const reservedSlugs = ['new', 'create', 'edit', 'show', 'crm', 'hr', 'pos', 'survey', 'inventory', 'sales', 'accounting', 'email-marketing', 'expenses', 'project-management', 'sms-marketing', 'time-management'];
    if (reservedSlugs.includes(brandSlugFromUrl)) return null;

    const matchingModule = subscribedModules.find(
      (m) => m.module_key === moduleKeyFromUrl && m.brand_slug === brandSlugFromUrl
    );

    if (matchingModule) {
      return {
        slug: brandSlugFromUrl,
        name: matchingModule.brand_name ?? brandSlugFromUrl,
        id: matchingModule.brand_id,
      };
    }

    return null;
  }, [brandSlugFromUrl, moduleKeyFromUrl, subscribedModules]);

  // Set error message based on brand resolution
  useEffect(() => {
    if (!brandSlugFromUrl || !moduleKeyFromUrl) {
      setBrandError(null);
      return;
    }

    const reservedSlugs = ['new', 'create', 'edit', 'show', 'crm', 'hr', 'pos', 'survey', 'inventory', 'sales', 'accounting', 'email-marketing', 'expenses', 'project-management', 'sms-marketing', 'time-management'];
    if (reservedSlugs.includes(brandSlugFromUrl)) {
      setBrandError(`"${brandSlugFromUrl}" is a reserved path segment and cannot be used as a brand slug. Please rename your brand to use a different slug.`);
      return;
    }

    if (!brandFilter) {
      setBrandError(`Brand "${brandSlugFromUrl}" not found in your subscribed modules. It may not be active or you don't have access to it.`);
    } else {
      setBrandError(null);
    }
  }, [brandSlugFromUrl, moduleKeyFromUrl, brandFilter]);

  const isBrandFiltered = useMemo(() => brandFilter !== null, [brandFilter]);

  // Build brand-scoped path for navigation
  const getBrandScopedPath = useCallback(
    (path: string): string => {
      if (!brandFilter || !moduleKeyFromUrl) return path;

      // If path already has brand in it, don't duplicate
      if (path.includes(`/${moduleKeyFromUrl}/${brandFilter.slug}`)) {
        return path;
      }

      // Insert brand slug into module paths
      // /dashboard/modules/crm/leads -> /dashboard/modules/crm/acme-corp/leads
      const modulePrefix = `/dashboard/modules/${moduleKeyFromUrl}`;
      if (path.startsWith(modulePrefix) && !path.includes(`/${moduleKeyFromUrl}/${brandFilter.slug}`)) {
        return path.replace(
          modulePrefix,
          `${modulePrefix}/${brandFilter.slug}`
        );
      }

      return path;
    },
    [brandFilter, moduleKeyFromUrl]
  );

  const setBrandFilter = useCallback(
    (filter: BrandFilter) => {
      if (!filter || !moduleKeyFromUrl) {
        // Navigate to unfiltered module path
        if (moduleKeyFromUrl) {
          router.push(`/dashboard/modules/${moduleKeyFromUrl}`);
        }
        return;
      }

      // Navigate to brand-scoped path
      router.push(`/dashboard/modules/${moduleKeyFromUrl}/${filter.slug}`);
    },
    [moduleKeyFromUrl, router]
  );

  const clearBrandFilter = useCallback(() => {
    setBrandError(null);
    if (moduleKeyFromUrl) {
      router.push(`/dashboard/modules/${moduleKeyFromUrl}`);
    }
  }, [moduleKeyFromUrl, router]);

  const value = useMemo(
    () => ({
      brandFilter,
      setBrandFilter,
      clearBrandFilter,
      isBrandFiltered,
      getBrandScopedPath,
      brandError,
    }),
    [brandFilter, setBrandFilter, clearBrandFilter, isBrandFiltered, getBrandScopedPath, brandError]
  );

  return (
    <BrandFilterContext.Provider value={value}>
      {children}
    </BrandFilterContext.Provider>
  );
}

export function useBrandFilter(): BrandFilterContextValue {
  const ctx = useContext(BrandFilterContext);
  if (!ctx) {
    return {
      brandFilter: null,
      setBrandFilter: () => {},
      clearBrandFilter: () => {},
      isBrandFiltered: false,
      getBrandScopedPath: (path: string) => path,
      brandError: null,
    };
  }
  return ctx;
}
