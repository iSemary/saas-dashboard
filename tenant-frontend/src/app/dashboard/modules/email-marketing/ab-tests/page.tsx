"use client";
import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, FieldDef } from "@/components/simple-crud-page";
import type { TableParams } from "@/lib/tenant-resources";
import { getEmAbTests, createEmAbTest, updateEmAbTest, deleteEmAbTest, type EmAbTest } from "@/lib/api-email-marketing";

const columns = (t: (k: string, f: string) => string): ColumnDef<EmAbTest>[] => [
  { accessorKey: "variant_name", header: t("email_marketing.variant", "Variant"), meta: { searchable: true } },
  { accessorKey: "campaign_id", header: t("email_marketing.campaign_id", "Campaign ID") },
  { accessorKey: "percentage", header: t("email_marketing.percentage", "Percentage") },
  { accessorKey: "winner", header: t("email_marketing.winner", "Winner") },
];

const fields: FieldDef[] = [
  { name: "campaign_id", label: "Campaign ID", type: "number", required: true },
  { name: "variant_name", label: "Variant Name", required: true },
  { name: "subject", label: "Subject" },
  { name: "body_html", label: "Body HTML", type: "textarea" },
  { name: "percentage", label: "Percentage", type: "number" },
];

export default function EmAbTestsPage() {
  return (
    <SimpleCRUDPage<EmAbTest>
      config={{
        titleKey: "email_marketing.ab_tests",
        titleFallback: "A/B Tests",
        subtitleKey: "email_marketing.ab_tests_subtitle",
        subtitleFallback: "Manage A/B tests",
        createLabelKey: "email_marketing.add_ab_test",
        createLabelFallback: "Add A/B Test",
        moduleKey: "email_marketing",
        dashboardHref: "/dashboard/modules/email-marketing",
        serverSide: true,
        fields,
        columns,
        listFn: (params?: TableParams) => getEmAbTests(params),
        createFn: createEmAbTest,
        updateFn: updateEmAbTest,
        deleteFn: deleteEmAbTest,
        toForm: (row) => ({
          campaign_id: row.campaign_id?.toString() ?? "", variant_name: row.variant_name ?? "",
          subject: "", body_html: row.body_html ?? "", percentage: row.percentage?.toString() ?? "50",
        }),
        fromForm: (form) => ({
          campaign_id: Number(form.campaign_id), variant_name: form.variant_name,
          subject: form.subject || undefined, body_html: form.body_html || undefined,
          percentage: form.percentage ? Number(form.percentage) : 50,
        }),
      }}
    />
  );
}
