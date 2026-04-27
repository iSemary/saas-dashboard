"use client";

import { useRouter } from "next/navigation";
import { ImportWizard } from "@/components/import";
import { Button } from "@/components/ui/button";
import { ArrowLeft } from "lucide-react";
import { useI18n } from "@/context/i18n-context";
import api from "@/lib/api";

export default function BranchesImportPage() {
  const { t } = useI18n();
  const router = useRouter();

  const uploadFn = async (file: File) => {
    const formData = new FormData();
    formData.append("file", file);

    const response = await api.post("/import/branches/upload", formData, {
      headers: {
        "Content-Type": "multipart/form-data",
      },
    });

    return response.data.data;
  };

  const confirmFn = async (validRows: unknown[]) => {
    const response = await api.post("/import/branches/confirm", {
      valid_rows: validRows,
    });

    return response.data;
  };

  const checkStatusFn = async (jobId: string) => {
    const response = await api.get(`/import/jobs/${jobId}/status`);
    return response.data;
  };

  const downloadTemplateFn = async () => {
    const response = await api.get("/import/branches/template", {
      responseType: "blob",
    });
    return response.data;
  };

  return (
    <div className="space-y-6">
      <div className="flex items-center gap-4">
        <Button
          type="button"
          variant="outline"
          size="sm"
          className="gap-2"
          onClick={() => router.push("/dashboard/branches")}
        >
          <ArrowLeft className="size-4" />
          {t("import.back_to_list", "Back to Branches")}
        </Button>
      </div>

      <div className="rounded-xl border bg-muted/40 p-6">
        <h1 className="text-2xl font-semibold">
          {t("import.branches.title", "Import Branches")}
        </h1>
        <p className="mt-1 text-muted-foreground">
          {t("import.branches.description", "Upload an Excel or CSV file to import branches in bulk")}
        </p>
      </div>

      <div className="rounded-xl border bg-card p-6">
        <ImportWizard
          entity="branches"
          uploadFn={uploadFn}
          confirmFn={confirmFn}
          checkStatusFn={checkStatusFn}
          downloadTemplateFn={downloadTemplateFn}
          onComplete={() => router.push("/dashboard/branches")}
        />
      </div>
    </div>
  );
}
