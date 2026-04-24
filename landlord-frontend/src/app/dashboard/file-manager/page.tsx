"use client";

import { useCallback, useEffect, useState } from "react";
import { Folder, File, Loader2 } from "lucide-react";
import api from "@/lib/api";
import { useI18n } from "@/context/i18n-context";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";

type FileEntry = {
  name: string;
  type: "file" | "directory";
  path: string;
  size?: number;
  modified?: string;
};

export default function FileManagerPage() {
  const { t } = useI18n();
  const [entries, setEntries] = useState<FileEntry[]>([]);
  const [currentPath, setCurrentPath] = useState("/");
  const [loading, setLoading] = useState(true);

  const load = useCallback(async (path: string) => {
    setLoading(true);
    try {
      const res = await api.get("/file-manager", { params: { path } });
      setEntries(Array.isArray(res.data) ? (res.data as FileEntry[]) : []);
    } catch {
      setEntries([]);
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    void load(currentPath);
  }, [load, currentPath]);

  const navigate = (entry: FileEntry) => {
    if (entry.type === "directory") {
      setCurrentPath(entry.path);
    }
  };

  const goUp = () => {
    const parts = currentPath.split("/").filter(Boolean);
    parts.pop();
    setCurrentPath("/" + parts.join("/"));
  };

  return (
    <div className="space-y-4">
      <div className="rounded-xl border bg-muted/40 p-4">
        <h1 className="text-xl font-semibold">{t("dashboard.file_manager.title", "File Manager")}</h1>
        <p className="mt-1 text-sm text-muted-foreground">
          {t("dashboard.file_manager.subtitle", "Browse and manage uploaded files.")}
        </p>
      </div>

      <Card>
        <CardHeader>
          <CardTitle className="text-sm font-mono">{currentPath}</CardTitle>
        </CardHeader>
        <CardContent>
          {loading ? (
            <div className="flex items-center justify-center py-8">
              <Loader2 className="size-6 animate-spin text-muted-foreground" />
            </div>
          ) : entries.length === 0 ? (
            <p className="text-sm text-muted-foreground py-8 text-center">
              {t("dashboard.file_manager.empty", "No files found.")}
            </p>
          ) : (
            <div className="space-y-1">
              {currentPath !== "/" && (
                <button
                  type="button"
                  className="flex w-full items-center gap-2 rounded-md px-3 py-2 text-sm hover:bg-muted"
                  onClick={goUp}
                >
                  <Folder className="size-4 text-muted-foreground" />
                  ..
                </button>
              )}
              {entries.map((entry) => (
                <button
                  key={entry.path}
                  type="button"
                  className="flex w-full items-center gap-2 rounded-md px-3 py-2 text-sm hover:bg-muted"
                  onClick={() => navigate(entry)}
                >
                  {entry.type === "directory" ? (
                    <Folder className="size-4 text-muted-foreground" />
                  ) : (
                    <File className="size-4 text-muted-foreground" />
                  )}
                  <span className="flex-1 text-left">{entry.name}</span>
                  {entry.size != null && (
                    <span className="text-xs text-muted-foreground">
                      {(entry.size / 1024).toFixed(1)} KB
                    </span>
                  )}
                </button>
              ))}
            </div>
          )}
        </CardContent>
      </Card>
    </div>
  );
}
