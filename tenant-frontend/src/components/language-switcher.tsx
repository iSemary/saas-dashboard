"use client";

import { useI18n } from "@/context/i18n-context";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";

const LOCALES = [
  { code: "en", label: "EN" },
  { code: "ar", label: "AR" },
] as const;

export function LanguageSwitcher() {
  const { locale, setLocale, t } = useI18n();

  return (
    <Select
      value={locale}
      onValueChange={(v) => {
        if (v === "en" || v === "ar") setLocale(v);
      }}
    >
      <SelectTrigger
        size="sm"
        className="h-8 w-[88px] border-border/80 bg-background/80"
        aria-label={t("dashboard.language.aria_label", "Language")}
      >
        <SelectValue placeholder={t("dashboard.language.placeholder", "Language")} />
      </SelectTrigger>
      <SelectContent align="end">
        {LOCALES.map((l) => (
          <SelectItem key={l.code} value={l.code}>
            {l.label}
          </SelectItem>
        ))}
      </SelectContent>
    </Select>
  );
}
