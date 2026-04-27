"use client";

import { useRouter, useParams } from "next/navigation";
import { ImportWizard, ImportSampleViewer } from "@/components/import";
import { Button } from "@/components/ui/button";
import { ArrowLeft, Upload } from "lucide-react";
import { useI18n } from "@/context/i18n-context";
import {
  uploadImportFile,
  confirmImport,
  checkImportStatus,
  downloadImportTemplate,
} from "@/lib/import-export";

// Entity display names
const ENTITY_NAMES: Record<string, string> = {
  branches: "Branches",
  brands: "Brands",
  tenants: "Tenants",
  users: "Users",
  currencies: "Currencies",
  countries: "Countries",
  provinces: "Provinces",
  cities: "Cities",
  categories: "Categories",
  tags: "Tags",
  types: "Types",
  units: "Units",
  industries: "Industries",
  releases: "Releases",
  "static-pages": "Static Pages",
  announcements: "Announcements",
  languages: "Languages",
  "email-templates": "Email Templates",
};

export default function GenericImportPage() {
  const { t } = useI18n();
  const router = useRouter();
  const params = useParams();
  const entity = params.entity as string;
  const entityName = ENTITY_NAMES[entity] || entity;

  const handleUpload = async (file: File) => {
    return uploadImportFile(entity, file);
  };

  const handleConfirm = async (validRows: unknown[]) => {
    return confirmImport(entity, validRows as any[]);
  };

  const handleCheckStatus = async (jobId: string) => {
    return checkImportStatus(entity, jobId);
  };

  const handleDownloadTemplate = async () => {
    return downloadImportTemplate(entity);
  };

  return (
    <div className="space-y-6">
      <div className="flex items-center gap-4">
        <Button
          type="button"
          variant="outline"
          size="sm"
          className="gap-2"
          onClick={() => router.push(`/dashboard/${entity}`)}
        >
          <ArrowLeft className="size-4" />
          {t("import.back_to_list", `Back to ${entityName}`)}
        </Button>
      </div>

      <div className="rounded-xl border bg-muted/40 p-6">
        <div className="flex items-center gap-3">
          <Upload className="size-8 text-primary" />
          <div>
            <h1 className="text-2xl font-semibold">
              {t("import.title", `Import ${entityName}`)}
            </h1>
            <p className="mt-1 text-muted-foreground">
              {t(
                "import.description",
                `Upload an Excel or CSV file to import ${entityName.toLowerCase()} in bulk`
              )}
            </p>
          </div>
        </div>
      </div>

      {/* Sample Data Preview */}
      <ImportSampleViewer entity={entity} entityName={entityName} />

      {/* Import Wizard */}
      <div className="rounded-xl border bg-card p-6">
        <ImportWizard
          entity={entity}
          uploadFn={handleUpload}
          confirmFn={handleConfirm}
          checkStatusFn={handleCheckStatus}
          downloadTemplateFn={handleDownloadTemplate}
          onComplete={() => router.push(`/dashboard/${entity}`)}
        />
      </div>
    </div>
  );
}
