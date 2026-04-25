"use client";

import { Warehouse } from "lucide-react";
import { ModulePageHeader } from "@/components/module-page-header";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";

const quickLinks = [
  { label: "Warehouses", href: "/dashboard/modules/inventory/warehouses", desc: "Manage storage locations" },
  { label: "Stock Moves", href: "/dashboard/modules/inventory/stock-moves", desc: "Track inbound & outbound stock" },
];

export default function InventoryPage() {
  return (
    <div className="space-y-4">
      <ModulePageHeader
        icon={Warehouse}
        titleKey="dashboard.inventory.title"
        titleFallback="Inventory"
        subtitleKey="dashboard.inventory.subtitle"
        subtitleFallback="Warehouse and stock management"
        dashboardHref="/dashboard/modules/inventory"
        moduleKey="inventory"
      />
      <div className="grid gap-4 sm:grid-cols-2">
        {quickLinks.map((link) => (
          <a key={link.href} href={link.href}>
            <Card className="hover:bg-accent transition-colors cursor-pointer">
              <CardHeader className="pb-1"><CardTitle className="text-base">{link.label}</CardTitle></CardHeader>
              <CardContent><p className="text-sm text-muted-foreground">{link.desc}</p></CardContent>
            </Card>
          </a>
        ))}
      </div>
    </div>
  );
}
