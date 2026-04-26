"use client";
import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, FieldDef } from "@/components/simple-crud-page";
import type { TableParams } from "@/lib/tenant-resources";
import { listTmPolicies, createTmPolicy, updateTmPolicy, deleteTmPolicy } from "@/lib/tm-resources";

type Policy = { id: number; name: string; policy_type: string; is_active: boolean; effective_from: string };

const columns = (t: (k: string, f: string) => string): ColumnDef<Policy>[] => [
  { accessorKey: "name", header: t("tm.name", "Name"), meta: { searchable: true } },
  { accessorKey: "policy_type", header: t("tm.type", "Type") },
  { accessorKey: "is_active", header: t("tm.active", "Active"), cell: ({ row }) => row.original.is_active ? "Yes" : "No" },
  { accessorKey: "effective_from", header: t("tm.effective_from", "Effective From") },
];

const fields: FieldDef[] = [
  { name: "name", label: "Name", required: true },
  { name: "policy_type", label: "Type", type: "select", required: true, options: [
    { value: "overtime", label: "Overtime" }, { value: "attendance", label: "Attendance" },
    { value: "timesheet", label: "Timesheet" }, { value: "leave", label: "Leave" },
  ]},
  { name: "is_active", label: "Active", type: "select", options: [
    { value: "1", label: "Yes" }, { value: "0", label: "No" },
  ]},
  { name: "effective_from", label: "Effective From" },
  { name: "description", label: "Description", type: "textarea" },
  { name: "rules", label: "Rules (JSON)", type: "textarea" },
];

export default function PoliciesPage() {
  return (
    <SimpleCRUDPage<Policy>
      config={{
        titleKey: "tm.policies",
        titleFallback: "Policies",
        subtitleKey: "tm.policies_subtitle",
        subtitleFallback: "Manage time management policies",
        createLabelKey: "tm.add_policy",
        createLabelFallback: "Add Policy",
        moduleKey: "time_management",
        dashboardHref: "/dashboard/modules/time-management",
        serverSide: true,
        fields,
        columns,
        listFn: (params?: TableParams) => listTmPolicies<Policy>(params),
        createFn: createTmPolicy,
        updateFn: (id: number, p: Record<string, unknown>) => updateTmPolicy(id, p),
        deleteFn: deleteTmPolicy,
        toForm: (row) => ({
          name: row.name ?? "", policy_type: row.policy_type ?? "overtime",
          is_active: row.is_active ? "1" : "0", effective_from: row.effective_from ?? "",
        }),
        fromForm: (form) => ({
          name: form.name, policy_type: form.policy_type || "overtime",
          is_active: form.is_active === "1", effective_from: form.effective_from || undefined,
          description: form.description || undefined,
        }),
      }}
    />
  );
}
