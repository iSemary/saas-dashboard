"use client";

import { useState } from "react";
import { Loader2, ArrowLeft, Plus } from "lucide-react";
import { useRouter } from "next/navigation";
import { toast } from "sonner";
import api from "@/lib/api";
import { useI18n } from "@/context/i18n-context";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Checkbox } from "@/components/ui/checkbox";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Alert, AlertDescription } from "@/components/ui/alert";
import { Info } from "lucide-react";

export default function BulkCreatePermissionsPage() {
  const { t } = useI18n();
  const router = useRouter();
  const [resource, setResource] = useState("");
  const [actions, setActions] = useState<string[]>(["view", "create", "update", "delete"]);
  const [loading, setLoading] = useState(false);

  const toggleAction = (action: string) => {
    setActions((prev) =>
      prev.includes(action) ? prev.filter((a) => a !== action) : [...prev, action]
    );
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!resource.trim()) {
      toast.error(t("dashboard.permissions.resource_required", "Resource name is required"));
      return;
    }
    if (actions.length === 0) {
      toast.error(t("dashboard.permissions.actions_required", "At least one action must be selected"));
      return;
    }

    setLoading(true);
    try {
      await api.post("/permissions/bulk-store", {
        resource: resource.trim(),
        actions,
      });
      toast.success(t("dashboard.permissions.created_successfully", "Permissions created successfully"));
      router.push("/dashboard/permissions");
    } catch (error: unknown) {
      const message = error instanceof Error ? error.message : t("dashboard.permissions.create_failed", "Failed to create permissions");
      toast.error(message);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="space-y-4">
      <div className="flex items-center gap-4">
        <Button
          type="button"
          variant="outline"
          size="sm"
          onClick={() => router.push("/dashboard/permissions")}
        >
          <ArrowLeft className="size-4" />
          {t("dashboard.actions.back", "Back")}
        </Button>
        <div className="flex-1">
          <h1 className="text-xl font-semibold">
            {t("dashboard.permissions.bulk_create", "Bulk Create")} - {t("dashboard.permissions.title", "Permissions")}
          </h1>
        </div>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>{t("dashboard.permissions.bulk_create", "Bulk Create Permissions")}</CardTitle>
          <CardDescription>
            {t("dashboard.permissions.bulk_create_desc", "Create multiple permissions at once for a resource")}
          </CardDescription>
        </CardHeader>
        <CardContent>
          <form onSubmit={handleSubmit} className="space-y-6">
            <div className="space-y-2">
              <Label htmlFor="resource">
                {t("dashboard.permissions.resource", "Resource")} <span className="text-destructive">*</span>
              </Label>
              <Input
                id="resource"
                value={resource}
                onChange={(e) => setResource(e.target.value)}
                placeholder="users"
                required
              />
              <p className="text-xs text-muted-foreground">
                {t("dashboard.permissions.resource_hint", "Enter resource name in lowercase (e.g., users, products)")}
              </p>
            </div>

            <div className="space-y-3">
              <Label>{t("dashboard.permissions.actions", "Actions")} <span className="text-destructive">*</span></Label>
              <div className="grid grid-cols-2 gap-4 sm:grid-cols-4">
                {["view", "create", "update", "delete"].map((action) => (
                  <div key={action} className="flex items-center space-x-2">
                    <Checkbox
                      id={`action-${action}`}
                      checked={actions.includes(action)}
                      onCheckedChange={() => toggleAction(action)}
                    />
                    <Label
                      htmlFor={`action-${action}`}
                      className="cursor-pointer capitalize"
                    >
                      {t(`dashboard.permissions.${action}`, action)}
                    </Label>
                  </div>
                ))}
              </div>
            </div>

            <Alert>
              <Info className="size-4" />
              <AlertDescription>
                <strong>{t("dashboard.permissions.example", "Example")}:</strong>{" "}
                {t("dashboard.permissions.resource", "Resource")} = &quot;users&quot;, {t("dashboard.permissions.actions", "Actions")} = view, create, update, delete
                <br />
                <strong>{t("dashboard.permissions.will_create", "Will create")}:</strong> view.users, create.users, update.users, delete.users
              </AlertDescription>
            </Alert>

            <div className="flex gap-2">
              <Button type="submit" disabled={loading || !resource.trim() || actions.length === 0}>
                {loading ? (
                  <Loader2 className="size-4 animate-spin" />
                ) : (
                  <Plus className="size-4" />
                )}
                {t("dashboard.permissions.create_permissions", "Create Permissions")}
              </Button>
              <Button
                type="button"
                variant="outline"
                onClick={() => router.push("/dashboard/permissions")}
              >
                {t("dashboard.actions.cancel", "Cancel")}
              </Button>
            </div>
          </form>
        </CardContent>
      </Card>
    </div>
  );
}
