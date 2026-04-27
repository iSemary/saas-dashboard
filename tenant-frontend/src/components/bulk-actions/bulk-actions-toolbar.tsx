"use client";

import { useState } from "react";
import { CheckCircle, XCircle, Trash2, Download, Loader2 } from "lucide-react";
import { Button } from "@/components/ui/button";
import { ConfirmDialog } from "@/components/ui/confirm-dialog";
import { useI18n } from "@/context/i18n-context";
import { toast } from "sonner";

export type BulkActionType = 
  | "delete" 
  | "activate" 
  | "deactivate" 
  | "assign" 
  | "change_status" 
  | "change_priority" 
  | "export";

export interface BulkActionConfig {
  id: BulkActionType;
  labelKey: string;
  labelFallback: string;
  icon: React.ComponentType<{ className?: string }>;
  variant?: "default" | "outline" | "destructive" | "secondary" | "ghost" | "link";
  requiresConfirmation?: boolean;
  confirmationTitleKey?: string;
  confirmationTitleFallback?: string;
  confirmationDescriptionKey?: string;
  confirmationDescriptionFallback?: string;
}

interface BulkActionsToolbarProps {
  selectedCount: number;
  selectedIds: number[];
  actions: BulkActionConfig[];
  onAction: (action: BulkActionType, ids: number[]) => Promise<void>;
  onExport?: (ids: number[]) => void;
  entityNameKey?: string;
  entityNameFallback?: string;
}

const DEFAULT_ACTIONS: BulkActionConfig[] = [
  {
    id: "activate",
    labelKey: "dashboard.bulk_actions.activate",
    labelFallback: "Activate",
    icon: CheckCircle,
    variant: "outline",
  },
  {
    id: "deactivate",
    labelKey: "dashboard.bulk_actions.deactivate",
    labelFallback: "Deactivate",
    icon: XCircle,
    variant: "outline",
  },
  {
    id: "delete",
    labelKey: "dashboard.bulk_actions.delete",
    labelFallback: "Delete",
    icon: Trash2,
    variant: "destructive",
    requiresConfirmation: true,
    confirmationTitleKey: "dashboard.bulk_actions.confirm_delete_title",
    confirmationTitleFallback: "Confirm Delete",
    confirmationDescriptionKey: "dashboard.bulk_actions.confirm_delete_description",
    confirmationDescriptionFallback: "Are you sure you want to delete the selected items?",
  },
  {
    id: "export",
    labelKey: "dashboard.bulk_actions.export",
    labelFallback: "Export",
    icon: Download,
    variant: "outline",
  },
];

export function BulkActionsToolbar({
  selectedCount,
  selectedIds,
  actions = DEFAULT_ACTIONS,
  onAction,
  onExport,
  entityNameKey = "dashboard.bulk_actions.items",
  entityNameFallback = "items",
}: BulkActionsToolbarProps) {
  const { t } = useI18n();
  const [confirmAction, setConfirmAction] = useState<BulkActionType | null>(null);
  const [loading, setLoading] = useState(false);

  if (selectedCount === 0) return null;

  const handleAction = async (action: BulkActionType) => {
    const actionConfig = actions.find((a) => a.id === action);
    
    if (actionConfig?.requiresConfirmation) {
      setConfirmAction(action);
      return;
    }

    if (action === "export" && onExport) {
      onExport(selectedIds);
      return;
    }

    await executeAction(action);
  };

  const executeAction = async (action: BulkActionType) => {
    setLoading(true);
    try {
      await onAction(action, selectedIds);
      toast.success(
        t("dashboard.bulk_actions.success", "Action completed successfully")
      );
    } catch {
      toast.error(
        t("dashboard.bulk_actions.error", "Action failed")
      );
    } finally {
      setLoading(false);
      setConfirmAction(null);
    }
  };

  const confirmActionConfig = actions.find((a) => a.id === confirmAction);

  return (
    <>
      <div className="fixed bottom-6 left-1/2 z-50 flex -translate-x-1/2 items-center gap-2 rounded-lg border border-border/70 bg-popover px-4 py-3 shadow-lg">
        <span className="text-sm text-muted-foreground">
          <strong>{selectedCount}</strong>{" "}
          {t(entityNameKey, entityNameFallback)}{" "}
          {t("dashboard.bulk_actions.selected", "selected")}
        </span>
        <div className="h-4 w-px bg-border" />
        <div className="flex items-center gap-1">
          {actions.map((action) => {
            const Icon = action.icon;
            return (
              <Button
                key={action.id}
                type="button"
                size="sm"
                variant={action.variant || "outline"}
                className="h-8 gap-1"
                onClick={() => handleAction(action.id)}
                disabled={loading}
              >
                {loading && confirmAction === action.id ? (
                  <Loader2 className="size-3.5 animate-spin" />
                ) : (
                  <Icon className="size-3.5" />
                )}
                {t(action.labelKey, action.labelFallback)}
              </Button>
            );
          })}
        </div>
      </div>

      {confirmActionConfig && (
        <ConfirmDialog
          open={!!confirmAction}
          onOpenChange={(open) => !open && setConfirmAction(null)}
          title={t(
            confirmActionConfig.confirmationTitleKey!,
            confirmActionConfig.confirmationTitleFallback!
          )}
          description={t(
            confirmActionConfig.confirmationDescriptionKey!,
            confirmActionConfig.confirmationDescriptionFallback!
          )}
          onConfirm={() => executeAction(confirmAction!)}
          confirmText={t("dashboard.actions.delete", "Delete")}
          cancelText={t("dashboard.actions.cancel", "Cancel")}
        />
      )}
    </>
  );
}
