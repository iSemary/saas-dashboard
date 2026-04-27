"use client";

import { useMemo } from "react";
import { Warehouse } from "lucide-react";
import type { ResponsiveLayouts } from "react-grid-layout";
import { ModulePageHeader } from "@/components/module-page-header";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import DraggableDashboardGrid from "@/components/dashboard/DraggableDashboardGrid";

const STORAGE_KEY = "dashboard_layout_inventory";

function buildDefaultLayouts(): ResponsiveLayouts {
  const keys = ["warehouses", "stock_moves"];
  const lg = keys.map((key, i) => ({
    i: key, x: i * 6, y: 0, w: 6, h: 3, minH: 2, minW: 2,
  }));
  const md = keys.map((key, i) => ({
    i: key, x: i * 6, y: 0, w: 6, h: 3, minH: 2, minW: 2,
  }));
  const sm = keys.map((key, i) => ({
    i: key, x: 0, y: i * 3, w: 6, h: 3, minH: 2, minW: 2,
  }));
  const xs = keys.map((key, i) => ({
    i: key, x: 0, y: i * 3, w: 4, h: 3, minH: 2, minW: 2,
  }));
  return { lg, md, sm, xs };
}

const quickLinks = [
  { key: "warehouses", label: "Warehouses", href: "/dashboard/modules/inventory/warehouses", desc: "Manage storage locations" },
  { key: "stock_moves", label: "Stock Moves", href: "/dashboard/modules/inventory/stock-moves", desc: "Track inbound & outbound stock" },
];

export default function InventoryPage() {
  const defaultLayouts = useMemo(() => buildDefaultLayouts(), []);

  const linkCards = quickLinks.map((link) => (
    <div key={link.key} className="h-full">
      <a href={link.href} className="block h-full">
        <Card className="h-full hover:bg-accent transition-colors cursor-pointer">
          <CardHeader className="pb-1"><CardTitle className="text-base">{link.label}</CardTitle></CardHeader>
          <CardContent><p className="text-sm text-muted-foreground">{link.desc}</p></CardContent>
        </Card>
      </a>
    </div>
  ));

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
      <DraggableDashboardGrid storageKey={STORAGE_KEY} defaultLayouts={defaultLayouts}>
        {linkCards}
      </DraggableDashboardGrid>
    </div>
  );
}
