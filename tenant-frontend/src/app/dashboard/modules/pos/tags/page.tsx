"use client";

import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, SimpleCRUDConfig } from "@/components/simple-crud-page";
import {
  listPosTags,
  createPosTag,
  deletePosTag,
} from "@/lib/tenant-resources";

type Tag = { id: number; type: string; value: string; products_count?: number; created_at?: string };

const config: SimpleCRUDConfig<Tag> = {
  titleKey: "dashboard.pos.tags",
  titleFallback: "Product Tags",
  subtitleKey: "dashboard.pos.tags_subtitle",
  subtitleFallback: "Manage product tags for organization",
  createLabelKey: "dashboard.pos.create_tag",
  createLabelFallback: "New Tag",
  fields: [
    { name: "type", label: "Tag Type", placeholder: "e.g., color, size, brand", required: true },
    { name: "value", label: "Tag Value", placeholder: "e.g., red, XL, Nike", required: true },
  ],
  listFn: listPosTags as () => Promise<Tag[]>,
  createFn: createPosTag,
  updateFn: async () => { throw new Error("Update not supported for tags"); },
  deleteFn: deletePosTag as unknown as (id: number) => Promise<void>,
  moduleKey: "pos",
  dashboardHref: "/dashboard/modules/pos",
  columns: (t): Array<ColumnDef<Tag>> => [
    { accessorKey: "id", header: t("dashboard.table.id", "ID") },
    { accessorKey: "type", header: t("dashboard.pos.tag_type", "Type") },
    { accessorKey: "value", header: t("dashboard.pos.tag_value", "Value") },
    { accessorKey: "products_count", header: t("dashboard.pos.products_count", "Products") },
  ],
  toForm: (r) => ({ type: r.type, value: r.value }),
  fromForm: (f) => ({ type: f.type, value: f.value }),
};

export default function PosTagsPage() {
  return <SimpleCRUDPage config={config} />;
}
