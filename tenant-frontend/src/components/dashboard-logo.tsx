"use client";

import Image from "next/image";
import { cn } from "@/lib/utils";
import { useDashboardBranding } from "@/context/dashboard-branding-context";

export function DashboardLogo({ className }: { className?: string }) {
  const { logoSrc } = useDashboardBranding();
  const remote = /^https?:\/\//.test(logoSrc);

  if (remote) {
    return (
      <img
        src={logoSrc}
        alt=""
        width={32}
        height={32}
        className={cn("size-8 shrink-0 rounded-lg object-contain", className)}
      />
    );
  }

  return (
    <Image
      src={logoSrc}
      alt=""
      width={32}
      height={32}
      className={cn("size-8 shrink-0 rounded-lg object-cover", className)}
    />
  );
}
