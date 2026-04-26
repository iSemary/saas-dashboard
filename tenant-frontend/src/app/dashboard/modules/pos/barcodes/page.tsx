"use client";

import { useState } from "react";
import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, SimpleCRUDConfig } from "@/components/simple-crud-page";
import {
  listPosBarcodes,
  createPosBarcode,
  deletePosBarcode,
  searchPosBarcode,
} from "@/lib/tenant-resources";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Search, Barcode } from "lucide-react";
import { toast } from "sonner";

type Barcode = {
  id: number;
  barcode_number: string;
  product?: { name: string; id: number };
  created_at?: string;
};

const config: SimpleCRUDConfig<Barcode> = {
  titleKey: "dashboard.pos.barcodes",
  titleFallback: "Barcodes",
  subtitleKey: "dashboard.pos.barcodes_subtitle",
  subtitleFallback: "Manage product barcodes",
  createLabelKey: "dashboard.pos.create_barcode",
  createLabelFallback: "New Barcode",
  fields: [
    { name: "barcode_number", label: "Barcode Number", placeholder: "Scan or enter barcode", required: true },
    { name: "product_id", label: "Product ID", type: "number", placeholder: "e.g., 1", required: true },
  ],
  listFn: listPosBarcodes as () => Promise<Barcode[]>,
  createFn: createPosBarcode,
  updateFn: async () => { throw new Error("Update not supported for barcodes"); },
  deleteFn: deletePosBarcode as unknown as (id: number) => Promise<void>,
  moduleKey: "pos",
  dashboardHref: "/dashboard/modules/pos",
  columns: (t): Array<ColumnDef<Barcode>> => [
    { accessorKey: "id", header: t("dashboard.table.id", "ID") },
    { accessorKey: "barcode_number", header: t("dashboard.pos.barcode", "Barcode") },
    {
      accessorKey: "product",
      header: t("dashboard.pos.product", "Product"),
      cell: ({ row }) => row.original.product?.name ?? "—",
    },
  ],
  toForm: (r) => ({ barcode_number: r.barcode_number, product_id: String(r.product?.id ?? "") }),
  fromForm: (f) => ({ barcode_number: f.barcode_number, product_id: Number(f.product_id) }),
};

export default function PosBarcodesPage() {
  const [searchBarcode, setSearchBarcode] = useState("");
  const [searching, setSearching] = useState(false);

  const handleSearch = async () => {
    if (!searchBarcode.trim()) return;
    setSearching(true);
    try {
      const result = await searchPosBarcode(searchBarcode);
      if (result) {
        toast.success(`Found: ${result.product?.name || "Product"}`);
      } else {
        toast.error("Barcode not found");
      }
    } catch {
      toast.error("Search failed");
    } finally {
      setSearching(false);
    }
  };

  return (
    <div className="p-6 space-y-4">
      <div className="flex gap-2">
        <div className="relative flex-1 max-w-sm">
          <Barcode className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
          <Input
            placeholder="Search barcode..."
            value={searchBarcode}
            onChange={(e) => setSearchBarcode(e.target.value)}
            onKeyDown={(e) => e.key === "Enter" && handleSearch()}
            className="pl-9"
          />
        </div>
        <Button onClick={handleSearch} disabled={searching || !searchBarcode.trim()}>
          <Search className="w-4 h-4 mr-2" />
          {searching ? "Searching..." : "Search"}
        </Button>
      </div>
      <SimpleCRUDPage config={config} />
    </div>
  );
}
