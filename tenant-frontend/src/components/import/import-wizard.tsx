"use client";

import { useState, useCallback } from "react";
import { Upload, FileSpreadsheet, CheckCircle, AlertCircle, Loader2, Download, X } from "lucide-react";
import { Button } from "@/components/ui/button";
// Progress component not available - using custom progress indicator
import { useI18n } from "@/context/i18n-context";
import { toast } from "sonner";
import { cn } from "@/lib/utils";

interface ImportRow {
  row: number;
  data: Record<string, unknown>;
  errors?: string[];
}

interface ImportPreview {
  valid_rows: ImportRow[];
  invalid_rows: ImportRow[];
  total_rows: number;
  headers: string[];
}

interface ImportJob {
  id: string;
  status: "pending" | "processing" | "completed" | "failed";
  progress: number;
  success_count: number;
  error_count: number;
  errors?: string[];
}

type ImportStep = "upload" | "preview" | "progress" | "complete";

interface ImportWizardProps {
  entity: string;
  uploadFn: (file: File) => Promise<ImportPreview>;
  confirmFn: (validRows: ImportRow[]) => Promise<{ jobId: string }>;
  checkStatusFn: (jobId: string) => Promise<ImportJob>;
  downloadTemplateFn: () => Promise<Blob>;
  onComplete?: () => void;
}

export type { ImportRow, ImportPreview, ImportJob };

export function ImportWizard({
  entity,
  uploadFn,
  confirmFn,
  checkStatusFn,
  downloadTemplateFn,
  onComplete,
}: ImportWizardProps) {
  const { t } = useI18n();
  const [step, setStep] = useState<ImportStep>("upload");
  const [file, setFile] = useState<File | null>(null);
  const [preview, setPreview] = useState<ImportPreview | null>(null);
  const [job, setJob] = useState<ImportJob | null>(null);
  const [loading, setLoading] = useState(false);
  const [dragActive, setDragActive] = useState(false);

  const handleDrag = useCallback((e: React.DragEvent) => {
    e.preventDefault();
    e.stopPropagation();
    if (e.type === "dragenter" || e.type === "dragover") {
      setDragActive(true);
    } else if (e.type === "dragleave") {
      setDragActive(false);
    }
  }, []);

  const isValidFile = (file: File) => {
    const validTypes = [
      "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
      "application/vnd.ms-excel",
      "text/csv",
    ];
    return validTypes.includes(file.type) || file.name.endsWith(".csv") || file.name.endsWith(".xlsx");
  };

  const handleDrop = useCallback((e: React.DragEvent) => {
    e.preventDefault();
    e.stopPropagation();
    setDragActive(false);
    
    if (e.dataTransfer.files && e.dataTransfer.files[0]) {
      const droppedFile = e.dataTransfer.files[0];
      if (isValidFile(droppedFile)) {
        setFile(droppedFile);
      } else {
        toast.error(t("import.invalid_file_type", "Please upload an Excel or CSV file"));
      }
    }
  }, [t]);

  const handleFileSelect = (e: React.ChangeEvent<HTMLInputElement>) => {
    if (e.target.files && e.target.files[0]) {
      const selectedFile = e.target.files[0];
      if (isValidFile(selectedFile)) {
        setFile(selectedFile);
      } else {
        toast.error(t("import.invalid_file_type", "Please upload an Excel or CSV file"));
      }
    }
  };

  const handleUpload = async () => {
    if (!file) return;
    
    setLoading(true);
    try {
      const previewData = await uploadFn(file);
      setPreview(previewData);
      setStep("preview");
    } catch {
      toast.error(t("import.upload_failed", "Failed to upload file"));
    } finally {
      setLoading(false);
    }
  };

  const handleConfirm = async () => {
    if (!preview || preview.valid_rows.length === 0) return;
    
    setLoading(true);
    try {
      const { jobId } = await confirmFn(preview.valid_rows);
      setStep("progress");
      pollJobStatus(jobId);
    } catch {
      toast.error(t("import.confirm_failed", "Failed to start import"));
      setLoading(false);
    }
  };

  const pollJobStatus = async (jobId: string) => {
    const interval = setInterval(async () => {
      try {
        const jobData = await checkStatusFn(jobId);
        setJob(jobData);
        
        if (jobData.status === "completed" || jobData.status === "failed") {
          clearInterval(interval);
          setStep("complete");
          setLoading(false);
          if (jobData.status === "completed") {
            toast.success(t("import.completed", "Import completed successfully"));
          }
        }
      } catch {
        clearInterval(interval);
        toast.error(t("import.status_check_failed", "Failed to check import status"));
        setLoading(false);
      }
    }, 2000);
  };

  const handleDownloadTemplate = async () => {
    try {
      const blob = await downloadTemplateFn();
      const url = URL.createObjectURL(blob);
      const a = document.createElement("a");
      a.href = url;
      a.download = `${entity}-import-template.xlsx`;
      a.click();
      URL.revokeObjectURL(url);
    } catch {
      toast.error(t("import.template_download_failed", "Failed to download template"));
    }
  };

  const handleReset = () => {
    setStep("upload");
    setFile(null);
    setPreview(null);
    setJob(null);
  };

  const validCount = preview?.valid_rows.length ?? 0;
  const invalidCount = preview?.invalid_rows.length ?? 0;

  return (
    <div className="space-y-6">
      {/* Step 1: Upload */}
      {step === "upload" && (
        <div className="space-y-4">
          <div className="rounded-lg border border-border/70 bg-muted/40 p-4">
            <h3 className="font-medium">
              {t("import.download_template", "Download Template")}
            </h3>
            <p className="text-sm text-muted-foreground">
              {t("import.template_description", "Download a template file with the correct format")}
            </p>
            <Button
              type="button"
              variant="outline"
              className="mt-2 gap-2"
              onClick={handleDownloadTemplate}
            >
              <Download className="size-4" />
              {t("import.download_template_button", "Download Template")}
            </Button>
          </div>

          <div
            className={cn(
              "relative flex flex-col items-center justify-center gap-4 rounded-lg border-2 border-dashed p-12 transition-colors",
              dragActive
                ? "border-primary bg-primary/5"
                : "border-border/70 bg-muted/40 hover:bg-muted/60",
              file && "border-green-500 bg-green-50"
            )}
            onDragEnter={handleDrag}
            onDragLeave={handleDrag}
            onDragOver={handleDrag}
            onDrop={handleDrop}
          >
            <input
              type="file"
              accept=".xlsx,.xls,.csv"
              onChange={handleFileSelect}
              className="absolute inset-0 cursor-pointer opacity-0"
            />
            
            {file ? (
              <>
                <FileSpreadsheet className="size-12 text-green-500" />
                <div className="text-center">
                  <p className="font-medium">{file.name}</p>
                  <p className="text-sm text-muted-foreground">
                    {(file.size / 1024).toFixed(1)} KB
                  </p>
                </div>
                <Button
                  type="button"
                  variant="ghost"
                  size="sm"
                  className="gap-1"
                  onClick={(e) => {
                    e.stopPropagation();
                    setFile(null);
                  }}
                >
                  <X className="size-4" />
                  {t("import.remove_file", "Remove")}
                </Button>
              </>
            ) : (
              <>
                <Upload className="size-12 text-muted-foreground" />
                <div className="text-center">
                  <p className="font-medium">
                    {t("import.drag_drop", "Drag and drop your file here")}
                  </p>
                  <p className="text-sm text-muted-foreground">
                    {t("import.or_click", "or click to browse")}
                  </p>
                  <p className="mt-2 text-xs text-muted-foreground">
                    {t("import.supported_formats", "Supports .xlsx, .xls, .csv (max 10MB)")}
                  </p>
                </div>
              </>
            )}
          </div>

          <div className="flex justify-end">
            <Button
              type="button"
              onClick={handleUpload}
              disabled={!file || loading}
              className="gap-2"
            >
              {loading ? (
                <Loader2 className="size-4 animate-spin" />
              ) : (
                <Upload className="size-4" />
              )}
              {t("import.upload_and_preview", "Upload and Preview")}
            </Button>
          </div>
        </div>
      )}

      {/* Step 2: Preview */}
      {step === "preview" && preview && (
        <div className="space-y-4">
          <div className="flex items-center gap-4 rounded-lg border border-border/70 p-4">
            <div className="flex items-center gap-2">
              <CheckCircle className="size-5 text-green-500" />
              <span className="font-medium text-green-600">{validCount}</span>
              <span className="text-sm text-muted-foreground">
                {t("import.valid_rows", "valid rows")}
              </span>
            </div>
            <div className="h-4 w-px bg-border" />
            <div className="flex items-center gap-2">
              <AlertCircle className="size-5 text-red-500" />
              <span className="font-medium text-red-600">{invalidCount}</span>
              <span className="text-sm text-muted-foreground">
                {t("import.invalid_rows", "invalid rows")}
              </span>
            </div>
          </div>

          {invalidCount > 0 && (
            <div className="rounded-lg border border-red-200 bg-red-50 p-4">
              <p className="text-sm text-red-600">
                {t("import.fix_errors", "Please fix the errors in your file before importing")}
              </p>
            </div>
          )}

          {/* Preview Table */}
          <div className="max-h-96 overflow-auto rounded-lg border border-border/70">
            <table className="w-full text-sm">
              <thead className="sticky top-0 bg-muted">
                <tr>
                  <th className="px-3 py-2 text-left">{t("import.row", "Row")}</th>
                  {preview.headers.map((header) => (
                    <th key={header} className="px-3 py-2 text-left">{header}</th>
                  ))}
                  <th className="px-3 py-2 text-left">{t("import.status", "Status")}</th>
                </tr>
              </thead>
              <tbody>
                {preview.valid_rows.map((row) => (
                  <tr key={row.row} className="border-t border-border/60 bg-green-50/50">
                    <td className="px-3 py-2">{row.row}</td>
                    {preview.headers.map((header) => (
                      <td key={header} className="px-3 py-2">
                        {String(row.data[header] ?? "-")}
                      </td>
                    ))}
                    <td className="px-3 py-2">
                      <span className="inline-flex items-center gap-1 text-green-600">
                        <CheckCircle className="size-3.5" />
                        {t("import.valid", "Valid")}
                      </span>
                    </td>
                  </tr>
                ))}
                {preview.invalid_rows.map((row) => (
                  <tr key={row.row} className="border-t border-border/60 bg-red-50/50">
                    <td className="px-3 py-2">{row.row}</td>
                    {preview.headers.map((header) => (
                      <td key={header} className="px-3 py-2">
                        {String(row.data[header] ?? "-")}
                      </td>
                    ))}
                    <td className="px-3 py-2">
                      <span className="inline-flex items-center gap-1 text-red-600">
                        <AlertCircle className="size-3.5" />
                        {row.errors?.join(", ")}
                      </span>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>

          <div className="flex justify-between">
            <Button type="button" variant="outline" onClick={handleReset}>
              {t("import.start_over", "Start Over")}
            </Button>
            <Button
              type="button"
              onClick={handleConfirm}
              disabled={validCount === 0 || loading}
              className="gap-2"
            >
              {loading ? (
                <Loader2 className="size-4 animate-spin" />
              ) : (
                <CheckCircle className="size-4" />
              )}
              {t("import.confirm_import", `Import ${validCount} Rows`)}
            </Button>
          </div>
        </div>
      )}

      {/* Step 3: Progress */}
      {step === "progress" && job && (
        <div className="space-y-6 py-12 text-center">
          <Loader2 className="mx-auto size-12 animate-spin text-primary" />
          <div className="space-y-2">
            <p className="font-medium">
              {t("import.processing", "Processing import...")}
            </p>
            <p className="text-sm text-muted-foreground">
              {t("import.dont_close", "Please don't close this window")}
            </p>
          </div>
          <div className="mx-auto max-w-md space-y-2">
            <div className="h-2 w-full rounded-full bg-muted overflow-hidden">
              <div
                className="h-full bg-primary transition-all duration-300"
                style={{ width: `${job.progress}%` }}
              />
            </div>
            <div className="flex justify-between text-sm text-muted-foreground">
              <span>{job.progress}%</span>
              <span>
                {job.success_count} / {validCount} {t("import.rows_processed", "rows processed")}
              </span>
            </div>
          </div>
        </div>
      )}

      {/* Step 4: Complete */}
      {step === "complete" && job && (
        <div className="space-y-6 py-12 text-center">
          {job.status === "completed" ? (
            <>
              <CheckCircle className="mx-auto size-12 text-green-500" />
              <div className="space-y-2">
                <p className="font-medium text-lg">
                  {t("import.completed_title", "Import Completed!")}
                </p>
                <p className="text-muted-foreground">
                  {t("import.completed_description", `${job.success_count} rows imported successfully`)}
                </p>
              </div>
            </>
          ) : (
            <>
              <AlertCircle className="mx-auto size-12 text-red-500" />
              <div className="space-y-2">
                <p className="font-medium text-lg">
                  {t("import.failed_title", "Import Failed")}
                </p>
                <p className="text-muted-foreground">
                  {job.errors?.join(", ")}
                </p>
              </div>
            </>
          )}

          <div className="flex justify-center gap-2">
            <Button type="button" variant="outline" onClick={handleReset}>
              {t("import.import_more", "Import More")}
            </Button>
            {job.status === "completed" && onComplete && (
              <Button type="button" onClick={onComplete}>
                {t("import.done", "Done")}
              </Button>
            )}
          </div>
        </div>
      )}
    </div>
  );
}
