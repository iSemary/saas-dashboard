import api from "./api";
import { toast } from "sonner";

export interface ImportRow {
  row: number;
  data: Record<string, unknown>;
  errors?: string[];
}

export interface ImportPreview {
  valid_rows: ImportRow[];
  invalid_rows: ImportRow[];
  total_rows: number;
  headers: string[];
}

export interface ImportJob {
  id: string;
  status: "pending" | "processing" | "completed" | "failed";
  progress: number;
  success_count: number;
  error_count: number;
  errors?: string[];
}

/**
 * Upload file for import preview
 */
export async function uploadImportFile(
  entity: string,
  file: File
): Promise<ImportPreview> {
  const formData = new FormData();
  formData.append("file", file);

  try {
    const response = await api.post(`/import/${entity}/upload`, formData, {
      headers: {
        "Content-Type": "multipart/form-data",
      },
    });

    if (response.data.success) {
      return response.data.data;
    } else {
      throw new Error(response.data.message || "Upload failed");
    }
  } catch (error) {
    const message = error instanceof Error ? error.message : "Upload failed";
    toast.error(message);
    throw error;
  }
}

/**
 * Confirm and execute import
 */
export async function confirmImport(
  entity: string,
  validRows: ImportRow[]
): Promise<{ jobId: string }> {
  try {
    const response = await api.post(`/import/${entity}/confirm`, {
      valid_rows: validRows,
    });

    if (response.data.success) {
      toast.success("Import started");
      return { jobId: response.data.job_id || "sync" };
    } else {
      throw new Error(response.data.message || "Import failed");
    }
  } catch (error) {
    const message = error instanceof Error ? error.message : "Import failed";
    toast.error(message);
    throw error;
  }
}

/**
 * Check import job status
 */
export async function checkImportStatus(
  entity: string,
  jobId: string
): Promise<ImportJob> {
  try {
    const response = await api.get(`/import/jobs/${jobId}/status`);
    return response.data;
  } catch {
    // Return default status on error
    return {
      id: jobId,
      status: "processing",
      progress: 0,
      success_count: 0,
      error_count: 0,
    };
  }
}

/**
 * Download import template
 */
export async function downloadImportTemplate(entity: string): Promise<Blob> {
  try {
    const response = await api.get(`/import/${entity}/template`, {
      responseType: "blob",
    });
    return response.data;
  } catch (error) {
    const message = error instanceof Error ? error.message : "Download failed";
    toast.error(message);
    throw error;
  }
}

/**
 * Export all data from table
 */
export async function exportTableData(
  entity: string,
  format: "csv" | "xlsx" = "xlsx",
  filters?: Record<string, unknown>
): Promise<void> {
  try {
    const response = await api.post(
      `/export/${entity}`,
      { format, filters },
      { responseType: "blob" }
    );

    // Create download link
    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement("a");
    link.href = url;
    link.setAttribute(
      "download",
      `${entity}-export-${new Date().toISOString().split("T")[0]}.${format}`
    );
    document.body.appendChild(link);
    link.click();
    link.remove();

    toast.success("Export downloaded");
  } catch {
    toast.error("Export failed");
  }
}
