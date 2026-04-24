"use client";

import { APP_NAME } from "@/lib/app-config";
import { useI18n } from "@/context/i18n-context";

export function AppFooter() {
  const { t } = useI18n();
  const year = new Date().getFullYear();

  return (
    <footer className="shrink-0 border-t border-border/80 bg-background/80 py-3 text-center text-xs text-muted-foreground">
      © {year} {APP_NAME}. {t("dashboard.footer.all_rights_reserved", "All rights reserved.")}
    </footer>
  );
}
