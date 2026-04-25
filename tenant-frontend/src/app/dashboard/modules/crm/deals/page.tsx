"use client";

import { useEffect, useRef, useState } from "react";
import { toast } from "sonner";
import { Loader2, DollarSign, TrendingUp, Handshake } from "lucide-react";
import { Card, CardContent } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { ModulePageHeader } from "@/components/module-page-header";
import { getCrmPipeline, moveCrmOpportunityStage } from "@/lib/tenant-resources";

interface Deal {
  id: number;
  name: string;
  expected_revenue: number | null;
  probability: number | null;
  contact?: { name: string } | null;
  assignedUser?: { name: string } | null;
}

interface KanbanColumn {
  stage: string;
  label: string;
  color: string;
  probability: number;
  count: number;
  value: number;
  opportunities: Deal[];
}

const STAGE_HEADER_COLORS: Record<string, string> = {
  prospecting: "border-slate-400",
  qualification: "border-blue-400",
  proposal: "border-yellow-400",
  negotiation: "border-orange-400",
  closed_won: "border-green-500",
  closed_lost: "border-red-400",
};

export default function CrmDealsPage() {
  const [columns, setColumns] = useState<KanbanColumn[]>([]);
  const [loading, setLoading] = useState(true);
  const draggingDeal = useRef<{ id: number; fromStage: string } | null>(null);

  const reload = () => {
    setLoading(true);
    getCrmPipeline()
      .then((data) => setColumns((data as KanbanColumn[]) ?? []))
      .catch(() => toast.error("Failed to load pipeline"))
      .finally(() => setLoading(false));
  };

  useEffect(() => {
    getCrmPipeline()
      .then((data) => { setColumns((data as KanbanColumn[]) ?? []); setLoading(false); })
      .catch(() => { toast.error("Failed to load pipeline"); setLoading(false); });
  }, []);

  const handleDragStart = (dealId: number, fromStage: string) => {
    draggingDeal.current = { id: dealId, fromStage };
  };

  const handleDrop = async (toStage: string) => {
    const drag = draggingDeal.current;
    if (!drag || drag.fromStage === toStage) return;
    draggingDeal.current = null;

    // Optimistic update
    setColumns((prev) =>
      prev.map((col) => {
        if (col.stage === drag.fromStage) {
          return { ...col, opportunities: col.opportunities.filter((d) => d.id !== drag.id) };
        }
        if (col.stage === toStage) {
          const moved = prev.find((c) => c.stage === drag.fromStage)?.opportunities.find((d) => d.id === drag.id);
          return moved ? { ...col, opportunities: [...col.opportunities, moved] } : col;
        }
        return col;
      })
    );

    try {
      await moveCrmOpportunityStage(drag.id, toStage);
      toast.success("Deal moved");
    } catch {
      toast.error("Failed to move deal");
      reload();
    }
  };

  if (loading) {
    return (
      <div className="flex min-h-[300px] items-center justify-center">
        <Loader2 className="size-6 animate-spin text-muted-foreground" />
      </div>
    );
  }

  return (
    <div className="space-y-4">
      <ModulePageHeader
        icon={Handshake}
        titleKey="dashboard.crm.deals"
        titleFallback="Deals"
        subtitleKey="dashboard.crm.deals_subtitle"
        subtitleFallback="Drag and drop deals across pipeline stages"
        dashboardHref="/dashboard/modules/crm"
        moduleKey="crm"
      />
      <div className="flex gap-3 overflow-x-auto pb-4">
        {columns.map((col) => (
          <div
            key={col.stage}
            className="flex flex-col min-w-[240px] w-[240px] shrink-0"
            onDragOver={(e) => e.preventDefault()}
            onDrop={() => void handleDrop(col.stage)}
          >
            <div className={`rounded-t-lg border-t-4 bg-card px-3 py-2 border-x border-border ${STAGE_HEADER_COLORS[col.stage] ?? "border-slate-400"}`}>
              <div className="flex items-center justify-between">
                <span className="text-sm font-semibold">{col.label ?? col.stage.replace("_", " ")}</span>
                <Badge variant="secondary" className="text-xs">{col.count}</Badge>
              </div>
              <div className="flex items-center gap-2 mt-1 text-xs text-muted-foreground">
                <DollarSign className="size-3" />
                <span>${(col.value ?? 0).toLocaleString()}</span>
                <TrendingUp className="size-3 ml-1" />
                <span>{col.probability}%</span>
              </div>
            </div>
            <div className="flex flex-col gap-2 rounded-b-lg border border-t-0 border-border bg-muted/30 p-2 min-h-[200px]">
              {col.opportunities.map((deal) => (
                <Card
                  key={deal.id}
                  draggable
                  onDragStart={() => handleDragStart(deal.id, col.stage)}
                  className="cursor-grab active:cursor-grabbing shadow-sm hover:shadow-md transition-shadow"
                >
                  <CardContent className="p-3 space-y-1">
                    <p className="text-sm font-medium leading-snug">{deal.name}</p>
                    {deal.contact?.name && (
                      <p className="text-xs text-muted-foreground">{deal.contact.name}</p>
                    )}
                    {deal.expected_revenue != null && (
                      <p className="text-xs font-semibold text-green-600 dark:text-green-400">
                        ${Number(deal.expected_revenue).toLocaleString()}
                      </p>
                    )}
                    {deal.assignedUser?.name && (
                      <p className="text-xs text-muted-foreground">→ {deal.assignedUser.name}</p>
                    )}
                  </CardContent>
                </Card>
              ))}
              {col.opportunities.length === 0 && (
                <p className="text-xs text-muted-foreground text-center py-6">No deals</p>
              )}
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}
