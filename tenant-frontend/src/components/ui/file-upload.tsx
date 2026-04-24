"use client";

import { useState } from "react";
import { Dropzone, ExtFile, FileMosaic } from "@files-ui/react";
import api from "@/lib/api";
import { toast } from "sonner";
import { useI18n } from "@/context/i18n-context";

export interface FileUploadProps {
  onUploadComplete?: (files: Array<{ id: number; url: string; name: string }>) => void;
  onFileSelect?: (files: File[]) => void;
  accept?: string;
  maxFiles?: number;
  maxFileSize?: number; // in MB
  folderId?: number;
  uploadType?: "media" | "document";
  disabled?: boolean;
}

export function FileUpload({
  onUploadComplete,
  onFileSelect,
  accept,
  maxFiles = 5,
  maxFileSize = 10,
  folderId,
  uploadType = "media",
  disabled = false,
}: FileUploadProps) {
  const { t } = useI18n();
  const [files, setFiles] = useState<ExtFile[]>([]);
  const [uploading, setUploading] = useState(false);

  const updateFiles = (incommingFiles: ExtFile[]) => {
    setFiles(incommingFiles);
    
    // Extract native File objects for onFileSelect callback
    const nativeFiles = incommingFiles
      .map((f) => f.file)
      .filter((f): f is File => f !== undefined);
    
    if (nativeFiles.length > 0 && onFileSelect) {
      onFileSelect(nativeFiles);
    }
  };

  const removeFile = (id: string | number | undefined) => {
    setFiles(files.filter((x) => x.id !== id));
  };

  const handleUpload = async () => {
    if (files.length === 0) {
      toast.error(t("dashboard.file_upload.no_files", "No files selected"));
      return;
    }

    setUploading(true);
    const uploadedFiles: Array<{ id: number; url: string; name: string }> = [];

    try {
      if (files.length === 1) {
        // Single file upload
        const file = files[0].file;
        if (!file) throw new Error("No file");

        const formData = new FormData();
        formData.append("file", file);
        if (folderId) {
          formData.append("folder_id", folderId.toString());
        }

        const endpoint = uploadType === "document" ? "/documents/upload" : "/media/upload";
        const res = await api.post(endpoint, formData, {
          headers: { "Content-Type": "multipart/form-data" },
        });

        if (res.data?.data) {
          uploadedFiles.push({
            id: res.data.data.id,
            url: res.data.data.url,
            name: res.data.data.original_name || file.name,
          });
        }
      } else {
        // Bulk upload
        const formData = new FormData();
        files.forEach((extFile) => {
          if (extFile.file) {
            formData.append("files[]", extFile.file);
          }
        });
        if (folderId) {
          formData.append("folder_id", folderId.toString());
        }

        const endpoint = uploadType === "document" ? "/documents/upload" : "/media/upload/bulk";
        const res = await api.post(endpoint, formData, {
          headers: { "Content-Type": "multipart/form-data" },
        });

        if (res.data?.data && Array.isArray(res.data.data)) {
          res.data.data.forEach((item: { id: number; url: string; original_name?: string }) => {
            uploadedFiles.push({
              id: item.id,
              url: item.url,
              name: item.original_name || "unnamed",
            });
          });
        }
      }

      toast.success(t("dashboard.file_upload.success", `${uploadedFiles.length} file(s) uploaded successfully`));

      onUploadComplete?.(uploadedFiles);
      setFiles([]); // Clear files after successful upload
    } catch (error) {
      toast.error(t("dashboard.file_upload.error", "Upload failed"));
      console.error("Upload error:", error);
    } finally {
      setUploading(false);
    }
  };

  return (
    <div className="space-y-4">
      <Dropzone
        onChange={updateFiles}
        value={files}
        accept={accept}
        maxFiles={maxFiles}
        maxFileSize={maxFileSize * 1024 * 1024} // Convert MB to bytes
        disabled={disabled || uploading}
        label={t("dashboard.file_upload.dropzone_label", "Drag & drop files here or click to browse")}
        uploadIcon
      />
      
      {files.length > 0 && (
        <div className="space-y-2">
          <div className="flex flex-wrap gap-2">
            {files.map((file) => (
              <FileMosaic
                key={file.id}
                {...file}
                onDelete={removeFile}
                preview
                resultOnTooltip
              />
            ))}
          </div>
          
          <button
            onClick={handleUpload}
            disabled={uploading || disabled}
            className="inline-flex h-9 items-center justify-center rounded-md bg-primary px-4 text-sm font-medium text-primary-foreground transition-colors hover:bg-primary/90 disabled:opacity-50"
          >
            {uploading
              ? t("dashboard.file_upload.uploading", "Uploading...")
              : t("dashboard.file_upload.upload", `Upload ${files.length} file(s)`
            )}
          </button>
        </div>
      )}
    </div>
  );
}
