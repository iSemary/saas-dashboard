"use client";

import { useEffect, useState } from "react";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Alert, AlertDescription, AlertTitle } from "@/components/ui/alert";
import { FileSpreadsheet, FileText, Info, CheckCircle, AlertCircle } from "lucide-react";
import { toast } from "sonner";
import api from "@/lib/api";

interface ImportSampleViewerProps {
  entity: string;
  entityDisplayName?: string;
}

export function ImportSampleViewer({ entity, entityDisplayName }: ImportSampleViewerProps) {
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [sampleData, setSampleData] = useState<{
    headers: string[];
    sample_rows: string[][];
    required_fields: string[];
    optional_fields: string[];
    description: string;
    total_samples: number;
  } | null>(null);

  const displayName = entityDisplayName || entity.charAt(0).toUpperCase() + entity.slice(1);

  const loadSampleData = async () => {
    try {
      setLoading(true);
      setError(null);
      const response = await api.get(`/import-samples/${entity}/preview`);
      
      if (response.data.success) {
        setSampleData(response.data.data);
      } else {
        setError(response.data.message || "Failed to load sample data");
      }
    } catch {
      setError("No sample data available for this entity");
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    loadSampleData();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [entity]);

  const downloadSampleCsv = async () => {
    try {
      const response = await api.get(`/import-samples/${entity}/download-csv`, {
        responseType: "blob",
      });
      
      const url = window.URL.createObjectURL(new Blob([response.data]));
      const link = document.createElement("a");
      link.href = url;
      link.setAttribute("download", `${entity}-sample.csv`);
      document.body.appendChild(link);
      link.click();
      link.remove();
      
      toast.success("Sample CSV downloaded");
    } catch {
      toast.error("Failed to download sample");
    }
  };

  const downloadSampleExcel = async () => {
    try {
      const response = await api.get(`/import-samples/${entity}/download-excel`, {
        responseType: "blob",
      });
      
      const url = window.URL.createObjectURL(new Blob([response.data]));
      const link = document.createElement("a");
      link.href = url;
      link.setAttribute("download", `${entity}-sample.xlsx`);
      document.body.appendChild(link);
      link.click();
      link.remove();
      
      toast.success("Sample Excel file downloaded");
    } catch {
      toast.error("Failed to download sample");
    }
  };

  if (loading) {
    return (
      <Card className="animate-pulse">
        <CardHeader>
          <CardTitle className="h-6 w-48 bg-muted rounded" />
        </CardHeader>
        <CardContent className="space-y-4">
          <div className="h-4 w-full bg-muted rounded" />
          <div className="h-4 w-3/4 bg-muted rounded" />
          <div className="h-32 w-full bg-muted rounded" />
        </CardContent>
      </Card>
    );
  }

  if (error || !sampleData) {
    return (
      <Alert variant="destructive">
        <AlertCircle className="h-4 w-4" />
        <AlertTitle>No Sample Data Available</AlertTitle>
        <AlertDescription>
          {error || "Sample data is not configured for this entity type."}
        </AlertDescription>
      </Alert>
    );
  }

  return (
    <div className="space-y-6">
      {/* Info Alert */}
      <Alert>
        <Info className="h-4 w-4" />
        <AlertTitle>Import Template Guide</AlertTitle>
        <AlertDescription>
          {sampleData.description}. Download a sample file below to see the exact format required.
        </AlertDescription>
      </Alert>

      {/* Field Requirements */}
      <Card>
        <CardHeader>
          <CardTitle className="text-base">Required Fields</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="flex flex-wrap gap-2">
            {sampleData.required_fields.map((field) => (
              <Badge key={field} variant="default" className="bg-red-100 text-red-800 hover:bg-red-100">
                <AlertCircle className="h-3 w-3 mr-1" />
                {field}
              </Badge>
            ))}
          </div>
        </CardContent>
      </Card>

      {/* Optional Fields */}
      {sampleData.optional_fields.length > 0 && (
        <Card>
          <CardHeader>
            <CardTitle className="text-base">Optional Fields</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="flex flex-wrap gap-2">
              {sampleData.optional_fields.map((field) => (
                <Badge key={field} variant="secondary">
                  {field}
                </Badge>
              ))}
            </div>
          </CardContent>
        </Card>
      )}

      {/* Sample Data Preview */}
      <Card>
        <CardHeader className="flex flex-row items-center justify-between">
          <div>
            <CardTitle className="text-base">Sample Data Preview</CardTitle>
            <p className="text-sm text-muted-foreground mt-1">
              Showing {sampleData.sample_rows.length} of {sampleData.total_samples} sample rows
            </p>
          </div>
          <div className="flex gap-2">
            <Button
              variant="outline"
              size="sm"
              onClick={downloadSampleCsv}
              className="gap-2"
            >
              <FileText className="h-4 w-4" />
              Download CSV
            </Button>
            <Button
              variant="outline"
              size="sm"
              onClick={downloadSampleExcel}
              className="gap-2"
            >
              <FileSpreadsheet className="h-4 w-4" />
              Download Excel
            </Button>
          </div>
        </CardHeader>
        <CardContent>
          <div className="rounded-md border overflow-hidden">
            <Table>
              <TableHeader>
                <TableRow className="bg-muted/50">
                  {sampleData.headers.map((header, index) => (
                    <TableHead key={index} className="font-semibold text-xs">
                      <div className="flex items-center gap-1">
                        {header}
                        {sampleData.required_fields.includes(header) && (
                          <span className="text-red-500">*</span>
                        )}
                      </div>
                    </TableHead>
                  ))}
                </TableRow>
              </TableHeader>
              <TableBody>
                {sampleData.sample_rows.map((row, rowIndex) => (
                  <TableRow key={rowIndex}>
                    {row.map((cell, cellIndex) => (
                      <TableCell key={cellIndex} className="text-xs py-2">
                        {cell || "—"}
                      </TableCell>
                    ))}
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          </div>
        </CardContent>
      </Card>

      {/* Status Values Help */}
      <Card className="bg-blue-50 border-blue-200">
        <CardHeader>
          <CardTitle className="text-base text-blue-900">Common Status Values</CardTitle>
        </CardHeader>
        <CardContent className="text-sm text-blue-800 space-y-2">
          <div className="flex items-center gap-2">
            <CheckCircle className="h-4 w-4 text-green-600" />
            <span><strong>active, 1, yes, true</strong> — Will set status to active/enabled</span>
          </div>
          <div className="flex items-center gap-2">
            <AlertCircle className="h-4 w-4 text-red-600" />
            <span><strong>inactive, 0, no, false</strong> — Will set status to inactive/disabled</span>
          </div>
        </CardContent>
      </Card>
    </div>
  );
}
