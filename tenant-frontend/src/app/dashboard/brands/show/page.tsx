"use client";

import { Suspense, useEffect, useState } from "react";
import { useRouter, useSearchParams } from "next/navigation";
import { Loader2, ArrowLeft, Building2, CheckCircle, Info, Image as ImageIcon, ExternalLink } from "lucide-react";
import api from "@/lib/api";
import { useI18n } from "@/context/i18n-context";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Alert, AlertDescription } from "@/components/ui/alert";
import { Badge } from "@/components/ui/badge";
import { DataTable } from "@/components/data-table";
import { ColumnDef } from "@tanstack/react-table";

type Brand = {
  id: number;
  name: string;
  slug?: string;
  description?: string;
  website?: string;
  email?: string;
  phone?: string;
  address?: string;
  logo?: string;
  logo_url?: string;
  status?: string;
  is_active?: boolean;
  created_at: string;
  updated_at: string;
  branches_count?: number;
  active_branches_count?: number;
  branches?: Branch[];
};

type Branch = {
  id: number;
  name: string;
  code?: string;
  city?: string;
  state?: string;
  manager_name?: string;
  status?: string;
  created_at: string;
};

function BrandShowContent() {
  const { t } = useI18n();
  const router = useRouter();
  const searchParams = useSearchParams();
  const id = searchParams.get('id');
  const [brand, setBrand] = useState<Brand | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const loadBrand = async () => {
      const brandId = Number(id);
      if (isNaN(brandId) || !id) {
        setLoading(false);
        return;
      }

      try {
        const data = await api.get(`/tenant/brands/${brandId}`).then((r) => r.data?.data ?? r.data) as Brand;
        setBrand(data);
      } catch {
        setBrand(null);
      } finally {
        setLoading(false);
      }
    };

    void loadBrand();
  }, [id]);

  const branchColumns: ColumnDef<Branch>[] = [
    { accessorKey: "id", header: t("dashboard.table.id", "ID") },
    { accessorKey: "name", header: t("dashboard.table.name", "Name") },
    { accessorKey: "code", header: t("dashboard.table.code", "Code") },
    {
      accessorKey: "location",
      header: t("dashboard.table.location", "Location"),
      cell: ({ row }) => `${row.original.city || ""}, ${row.original.state || ""}`,
    },
    { accessorKey: "manager_name", header: t("dashboard.table.manager", "Manager") },
    {
      accessorKey: "status",
      header: t("dashboard.table.status", "Status"),
      cell: ({ row }) => (
        <Badge variant={row.original.status === "active" ? "default" : "secondary"}>
          {t(`dashboard.status.${row.original.status || "inactive"}`, row.original.status || "inactive")}
        </Badge>
      ),
    },
    {
      accessorKey: "created_at",
      header: t("dashboard.table.created_at", "Created At"),
      cell: ({ row }) => new Date(row.original.created_at).toLocaleString(),
    },
    {
      id: "actions",
      header: "",
      cell: ({ row }) => (
        <Button
          type="button"
          variant="outline"
          size="sm"
          onClick={() => router.push(`/dashboard/branches/${row.original.id}`)}
        >
          <ExternalLink className="size-3.5" />
        </Button>
      ),
    },
  ];

  if (loading) {
    return (
      <div className="flex min-h-[200px] items-center justify-center gap-2 text-muted-foreground">
        <Loader2 className="size-6 animate-spin" />
      </div>
    );
  }

  if (!brand) {
    return (
      <div className="space-y-4">
        <Button
          type="button"
          variant="outline"
          size="sm"
          onClick={() => router.push("/dashboard/brands")}
        >
          <ArrowLeft className="size-4" />
          {t("dashboard.actions.back", "Back")}
        </Button>
        <Card>
          <CardContent className="flex min-h-[200px] items-center justify-center">
            <p className="text-muted-foreground">{t("dashboard.brands.not_found", "Brand not found")}</p>
          </CardContent>
        </Card>
      </div>
    );
  }

  return (
    <div className="space-y-4">
      <div className="flex items-center gap-4">
        <Button
          type="button"
          variant="outline"
          size="sm"
          onClick={() => router.push("/dashboard/brands")}
        >
          <ArrowLeft className="size-4" />
          {t("dashboard.actions.back", "Back")}
        </Button>
        <div className="flex-1">
          <h1 className="text-xl font-semibold">{brand.name}</h1>
        </div>
        <Badge variant={brand.is_active ? "default" : "secondary"}>
          {t(`dashboard.status.${brand.is_active ? "active" : "inactive"}`, brand.is_active ? "active" : "inactive")}
        </Badge>
      </div>

      <div className="grid gap-4 lg:grid-cols-3">
        <div className="lg:col-span-2 space-y-4">
          <Card>
            <CardHeader>
              <CardTitle>{t("dashboard.brands.details", "Brand Details")}</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="grid gap-6 md:grid-cols-4">
                <div className="md:col-span-1">
                  {brand.logo || brand.logo_url ? (
                    <img
                      src={brand.logo_url || brand.logo}
                      alt={brand.name}
                      className="w-full h-32 object-contain rounded-lg border"
                    />
                  ) : (
                    <div className="flex flex-col items-center justify-center h-32 rounded-lg border border-dashed text-muted-foreground">
                      <ImageIcon className="size-8 mb-2" />
                      <p className="text-xs">{t("dashboard.brands.no_logo", "No logo")}</p>
                    </div>
                  )}
                </div>
                <div className="md:col-span-3 space-y-3">
                  <div className="flex justify-between text-sm">
                    <span className="text-muted-foreground">{t("dashboard.brands.name", "Name")}</span>
                    <span className="font-medium">{brand.name}</span>
                  </div>
                  <div className="flex justify-between text-sm">
                    <span className="text-muted-foreground">{t("dashboard.brands.slug", "Slug")}</span>
                    <code className="text-xs bg-muted px-2 py-1 rounded">{brand.slug || "—"}</code>
                  </div>
                  <div className="flex justify-between text-sm">
                    <span className="text-muted-foreground">{t("dashboard.brands.description", "Description")}</span>
                    <span className="text-right max-w-xs">{brand.description || t("dashboard.brands.no_description", "No description")}</span>
                  </div>
                  <div className="flex justify-between text-sm">
                    <span className="text-muted-foreground">{t("dashboard.brands.website", "Website")}</span>
                    {brand.website ? (
                      <a
                        href={brand.website}
                        target="_blank"
                        rel="noopener noreferrer"
                        className="text-blue-600 hover:underline flex items-center gap-1"
                      >
                        {brand.website}
                        <ExternalLink className="size-3" />
                      </a>
                    ) : (
                      <span className="text-muted-foreground">{t("dashboard.brands.not_provided", "Not provided")}</span>
                    )}
                  </div>
                  <div className="flex justify-between text-sm">
                    <span className="text-muted-foreground">{t("dashboard.brands.email", "Email")}</span>
                    <span>{brand.email || t("dashboard.brands.not_provided", "Not provided")}</span>
                  </div>
                  <div className="flex justify-between text-sm">
                    <span className="text-muted-foreground">{t("dashboard.brands.phone", "Phone")}</span>
                    <span>{brand.phone || t("dashboard.brands.not_provided", "Not provided")}</span>
                  </div>
                  <div className="flex justify-between text-sm">
                    <span className="text-muted-foreground">{t("dashboard.brands.address", "Address")}</span>
                    <span className="text-right max-w-xs">{brand.address || t("dashboard.brands.not_provided", "Not provided")}</span>
                  </div>
                  <div className="flex justify-between text-sm">
                    <span className="text-muted-foreground">{t("dashboard.brands.created_at", "Created At")}</span>
                    <span>{new Date(brand.created_at).toLocaleString()}</span>
                  </div>
                  <div className="flex justify-between text-sm">
                    <span className="text-muted-foreground">{t("dashboard.brands.updated_at", "Updated At")}</span>
                    <span>{new Date(brand.updated_at).toLocaleString()}</span>
                  </div>
                </div>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle>{t("dashboard.brands.branches", "Branches")}</CardTitle>
            </CardHeader>
            <CardContent>
              {brand.branches && brand.branches.length > 0 ? (
                <DataTable
                  columns={branchColumns}
                  data={brand.branches}
                  enableExport={false}
                  searchable={false}
                  serverSide={false}
                />
              ) : (
                <div className="flex min-h-[100px] items-center justify-center text-muted-foreground">
                  {t("dashboard.brands.no_branches", "No branches found")}
                </div>
              )}
            </CardContent>
          </Card>
        </div>

        <div className="space-y-4">
          <Card>
            <CardHeader>
              <CardTitle className="text-sm">{t("dashboard.brands.statistics", "Statistics")}</CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="flex items-center gap-4 p-3 rounded-lg bg-muted/50">
                <div className="flex size-10 items-center justify-center rounded-lg bg-blue-500/20">
                  <Building2 className="size-5 text-blue-600" />
                </div>
                <div>
                  <p className="text-xs text-muted-foreground">{t("dashboard.brands.total_branches", "Total Branches")}</p>
                  <p className="text-2xl font-semibold">{brand.branches_count || 0}</p>
                </div>
              </div>
              <div className="flex items-center gap-4 p-3 rounded-lg bg-muted/50">
                <div className="flex size-10 items-center justify-center rounded-lg bg-green-500/20">
                  <CheckCircle className="size-5 text-green-600" />
                </div>
                <div>
                  <p className="text-xs text-muted-foreground">{t("dashboard.brands.active_branches", "Active Branches")}</p>
                  <p className="text-2xl font-semibold">{brand.active_branches_count || 0}</p>
                </div>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle className="text-sm">{t("dashboard.brands.information", "Information")}</CardTitle>
            </CardHeader>
            <CardContent>
              <Alert>
                <Info className="size-4" />
                <AlertDescription>
                  {t("dashboard.brands.managed_by_landlord", "Brands are managed by the landlord")}
                </AlertDescription>
              </Alert>
              <p className="text-sm text-muted-foreground mt-2">
                {t("dashboard.brands.contact_landlord", "Contact the landlord to modify brand details")}
              </p>
            </CardContent>
          </Card>
        </div>
      </div>
    </div>
  );
}

export default function BrandShowPage() {
  return (
    <Suspense fallback={<div className="flex min-h-[200px] items-center justify-center"><Loader2 className="size-6 animate-spin" /></div>}>
      <BrandShowContent />
    </Suspense>
  );
}
