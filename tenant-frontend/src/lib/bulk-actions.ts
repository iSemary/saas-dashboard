import api from "./api";
import { toast } from "sonner";

export type BulkActionType = "delete" | "activate" | "deactivate" | "assign" | "change_status" | "change_priority" | "export";

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

export interface BulkActionResult {
  success: boolean;
  processed_count: number;
  success_count: number;
  error_count: number;
  errors: string[];
}

/**
 * Get available bulk actions for an entity
 */
export async function getAvailableActions(entity: string): Promise<BulkActionConfig[]> {
  try {
    const response = await api.get(`/bulk-actions/${entity}/actions`);
    return response.data.data || [];
  } catch {
    // Return default actions if API fails
    return [
      {
        id: "delete",
        labelKey: "dashboard.bulk_actions.delete",
        labelFallback: "Delete",
        icon: () => null,
        variant: "destructive",
        requiresConfirmation: true,
      },
      {
        id: "export",
        labelKey: "dashboard.bulk_actions.export",
        labelFallback: "Export",
        icon: () => null,
        variant: "outline",
      },
    ];
  }
}

/**
 * Execute bulk action on selected IDs
 */
export async function executeBulkAction(
  entity: string,
  action: BulkActionType,
  ids: number[],
  params?: Record<string, unknown>
): Promise<BulkActionResult> {
  try {
    const response = await api.post(`/bulk-actions/${entity}/execute`, {
      action,
      ids,
      params,
    });
    
    if (response.data.success) {
      toast.success(`Action completed: ${response.data.data.success_count} items processed`);
      return response.data.data;
    } else {
      toast.error(response.data.message || "Action failed");
      return {
        success: false,
        processed_count: 0,
        success_count: 0,
        error_count: ids.length,
        errors: [response.data.message || "Action failed"],
      };
    }
  } catch (error) {
    const message = error instanceof Error ? error.message : "Action failed";
    toast.error(message);
    return {
      success: false,
      processed_count: 0,
      success_count: 0,
      error_count: ids.length,
      errors: [message],
    };
  }
}

/**
 * Export selected IDs
 */
export async function exportSelected(
  entity: string,
  ids: number[],
  format: "csv" | "xlsx" = "xlsx"
): Promise<void> {
  try {
    const response = await api.post(
      `/bulk-actions/${entity}/export`,
      { ids, format },
      { responseType: "blob" }
    );
    
    // Create download link
    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement("a");
    link.href = url;
    link.setAttribute("download", `${entity}-export-${new Date().toISOString().split("T")[0]}.${format}`);
    document.body.appendChild(link);
    link.click();
    link.remove();
    
    toast.success("Export downloaded");
  } catch {
    toast.error("Export failed");
  }
}

/**
 * Get default bulk actions for an entity type
 */
export function getDefaultBulkActions(entityType: "standard" | "ticket" | "user" = "standard"): BulkActionConfig[] {
  const baseActions: BulkActionConfig[] = [
    {
      id: "delete",
      labelKey: "dashboard.bulk_actions.delete",
      labelFallback: "Delete",
      icon: () => null,
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
      icon: () => null,
      variant: "outline",
    },
  ];

  if (entityType === "standard") {
    return [
      {
        id: "activate",
        labelKey: "dashboard.bulk_actions.activate",
        labelFallback: "Activate",
        icon: () => null,
        variant: "outline",
      },
      {
        id: "deactivate",
        labelKey: "dashboard.bulk_actions.deactivate",
        labelFallback: "Deactivate",
        icon: () => null,
        variant: "outline",
      },
      ...baseActions,
    ];
  }

  if (entityType === "ticket") {
    return [
      {
        id: "assign",
        labelKey: "dashboard.bulk_actions.assign",
        labelFallback: "Assign To...",
        icon: () => null,
        variant: "outline",
      },
      {
        id: "change_status",
        labelKey: "dashboard.bulk_actions.change_status",
        labelFallback: "Change Status...",
        icon: () => null,
        variant: "outline",
      },
      {
        id: "change_priority",
        labelKey: "dashboard.bulk_actions.change_priority",
        labelFallback: "Change Priority...",
        icon: () => null,
        variant: "outline",
      },
      ...baseActions,
    ];
  }

  return baseActions;
}
