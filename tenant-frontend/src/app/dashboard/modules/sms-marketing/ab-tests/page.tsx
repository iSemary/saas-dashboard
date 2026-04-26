"use client";
import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, FieldDef } from "@/components/simple-crud-page";
import type { TableParams } from "@/lib/tenant-resources";
import { getSmAbTests, createSmAbTest, updateSmAbTest, deleteSmAbTest, type SmAbTest } from "@/lib/api-sms-marketing";

const columns = (t: (k: string, f: string) => string): ColumnDef<SmAbTest>[] => [
  { accessorKey: "variant_name", header: t("sms_marketing.variant", "Variant"), meta: { searchable: true } },
  { accessorKey: "campaign_id", header: t("sms_marketing.campaign_id", "Campaign ID") },
  { accessorKey: "percentage", header: t("sms_marketing.percentage", "Percentage") },
  { accessorKey: "winner", header: t("sms_marketing.winner", "Winner") },
];

const fields: FieldDef[] = [
  { name: "campaign_id", label: "Campaign ID", type: "number", required: true },
  { name: "variant_name", label: "Variant Name", required: true },
  { name: "body", label: "Body", type: "textarea" },
  { name: "percentage", label: "Percentage", type: "number" },
];

export default function SmAbTestsPage() {
  return (
    <SimpleCRUDPage<SmAbTest>
      config={{
        titleKey: "sms_marketing.ab_tests",
        titleFallback: "A/B Tests",
        subtitleKey: "sms_marketing.ab_tests_subtitle",
        subtitleFallback: "Manage A/B tests",
        createLabelKey: "sms_marketing.add_ab_test",
        createLabelFallback: "Add A/B Test",
        moduleKey: "sms_marketing",
        dashboardHref: "/dashboard/modules/sms-marketing",
        serverSide: true,
        fields,
        columns,
        listFn: (params?: TableParams) => getSmAbTests(params),
        createFn: createSmAbTest,
        updateFn: updateSmAbTest,
        deleteFn: deleteSmAbTest,
        toForm: (row) => ({
          campaign_id: row.campaign_id?.toString() ?? "", variant_name: row.variant_name ?? "",
          body: row.body ?? "", percentage: row.percentage?.toString() ?? "50",
        }),
        fromForm: (form) => ({
          campaign_id: Number(form.campaign_id), variant_name: form.variant_name,
          body: form.body || undefined, percentage: form.percentage ? Number(form.percentage) : 50,
        }),
      }}
    />
  );
}
