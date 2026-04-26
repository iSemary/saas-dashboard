"use client";

import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, SimpleCRUDConfig } from "@/components/simple-crud-page";
import {
  listCrmPipelineStages,
  createCrmPipelineStage,
  updateCrmPipelineStage,
  deleteCrmPipelineStage,
} from "@/lib/tenant-resources";

type PipelineStage = {
  id: number;
  name: string;
  probability: number;
  color?: string;
  order?: number;
  is_system?: boolean;
};

const config: SimpleCRUDConfig<PipelineStage> = {
  titleKey: "dashboard.crm.pipeline_stages",
  titleFallback: "Pipeline Stages",
  subtitleKey: "dashboard.crm.pipeline_stages_subtitle",
  subtitleFallback: "Customize your sales pipeline stages",
  createLabelKey: "dashboard.crm.add_stage",
  createLabelFallback: "Add Stage",
  fields: [
    { name: "name", label: "Stage Name", placeholder: "e.g., Prospecting", required: true },
    { name: "probability", label: "Probability (%)", type: "number", placeholder: "0-100", required: true },
    { name: "color", label: "Color", placeholder: "#3B82F6 or blue" },
    { name: "order", label: "Order", type: "number", placeholder: "1, 2, 3..." },
  ],
  listFn: listCrmPipelineStages as () => Promise<PipelineStage[]>,
  createFn: createCrmPipelineStage,
  updateFn: updateCrmPipelineStage,
  deleteFn: deleteCrmPipelineStage as unknown as (id: number) => Promise<void>,
  moduleKey: "crm",
  dashboardHref: "/dashboard/modules/crm",
  columns: (t): Array<ColumnDef<PipelineStage>> => [
    { accessorKey: "id", header: t("dashboard.table.id", "ID") },
    { accessorKey: "name", header: t("dashboard.crm.stage_name", "Name") },
    {
      accessorKey: "probability",
      header: t("dashboard.crm.probability", "Probability"),
      cell: ({ row }) => `${row.original.probability}%`,
    },
    {
      accessorKey: "color",
      header: t("dashboard.crm.color", "Color"),
      cell: ({ row }) =>
        row.original.color ? (
          <div className="flex items-center gap-2">
            <div
              className="w-4 h-4 rounded border"
              style={{ backgroundColor: row.original.color }}
            />
            <span className="text-xs text-muted-foreground">{row.original.color}</span>
          </div>
        ) : (
          "—"
        ),
    },
    { accessorKey: "order", header: t("dashboard.crm.order", "Order") },
  ],
  toForm: (r) => ({
    name: r.name ?? "",
    probability: String(r.probability ?? ""),
    color: r.color ?? "",
    order: r.order ? String(r.order) : "",
  }),
  fromForm: (f) => ({
    name: f.name,
    probability: Number(f.probability),
    color: f.color || undefined,
    order: f.order ? Number(f.order) : undefined,
  }),
};

export default function CrmPipelineStagesPage() {
  return <SimpleCRUDPage config={config} />;
}
