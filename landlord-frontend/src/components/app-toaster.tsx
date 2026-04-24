"use client";

import { Toaster } from "sonner";
import { useI18n } from "@/context/i18n-context";

/** English: bottom-right; Arabic (RTL): bottom-left */
export function AppToaster() {
  const { locale } = useI18n();
  const position = locale === "ar" ? "bottom-left" : "bottom-right";
  return <Toaster richColors position={position} />;
}
