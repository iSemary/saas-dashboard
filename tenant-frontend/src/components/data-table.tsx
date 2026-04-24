"use client";

import {
  ColumnFiltersState,
  ColumnDef,
  VisibilityState,
  flexRender,
  getFacetedRowModel,
  getFacetedUniqueValues,
  getCoreRowModel,
  getFilteredRowModel,
  getPaginationRowModel,
  getSortedRowModel,
  RowSelectionState,
  SortingState,
  PaginationState,
  useReactTable,
} from "@tanstack/react-table";
import { ReactNode, useState } from "react";
import { useI18n } from "@/context/i18n-context";
import * as XLSX from "xlsx";

interface Props<TData> {
  columns: Array<ColumnDef<TData>>;
  data: TData[];
  toolbarActions?: ReactNode;
  enableExport?: boolean;
}

export function DataTable<TData>({ columns, data, toolbarActions, enableExport = true }: Props<TData>) {
  const { t } = useI18n();
  const [sorting, setSorting] = useState<SortingState>([]);
  const [globalFilter, setGlobalFilter] = useState("");
  const [columnFilters, setColumnFilters] = useState<ColumnFiltersState>([]);
  const [columnVisibility, setColumnVisibility] = useState<VisibilityState>({});
  const [rowSelection, setRowSelection] = useState<RowSelectionState>({});
  const [pagination, setPagination] = useState<PaginationState>({ pageIndex: 0, pageSize: 10 });
  const table = useReactTable({
    columns,
    data,
    state: { sorting, globalFilter, columnFilters, columnVisibility, rowSelection, pagination },
    onSortingChange: setSorting,
    onGlobalFilterChange: setGlobalFilter,
    onColumnFiltersChange: setColumnFilters,
    onColumnVisibilityChange: setColumnVisibility,
    onRowSelectionChange: setRowSelection,
    onPaginationChange: setPagination,
    getCoreRowModel: getCoreRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    getFacetedRowModel: getFacetedRowModel(),
    getFacetedUniqueValues: getFacetedUniqueValues(),
    getSortedRowModel: getSortedRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    enableRowSelection: true,
  });
  const exportCsv = () => {
    const exportRows = table.getFilteredRowModel().rows;
    const cols = table.getVisibleLeafColumns().filter((c) => c.id !== "actions");
    const headers = cols.map((c) => c.id);
    const lines = exportRows.map((row) =>
      cols
        .map((c) => {
          const raw = row.getValue(c.id);
          const str = raw == null ? "" : String(raw);
          return `"${str.replace(/"/g, '""')}"`;
        })
        .join(","),
    );
    const csv = [headers.join(","), ...lines].join("\n");
    const blob = new Blob([csv], { type: "text/csv;charset=utf-8;" });
    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = "table-export.csv";
    a.click();
    URL.revokeObjectURL(url);
  };

  const exportXlsx = () => {
    const exportRows = table.getFilteredRowModel().rows;
    const cols = table.getVisibleLeafColumns().filter((c) => c.id !== "actions");
    const headers = cols.map((c) => c.columnDef.header?.toString() || c.id);

    const data = exportRows.map((row) => {
      const rowData: Record<string, unknown> = {};
      cols.forEach((col) => {
        const header = col.columnDef.header?.toString() || col.id;
        rowData[header] = row.getValue(col.id);
      });
      return rowData;
    });

    const ws = XLSX.utils.json_to_sheet(data, { header: headers });
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Export");
    XLSX.writeFile(wb, "table-export.xlsx");
  };

  return (
    <div className="space-y-3">
      <div className="flex flex-wrap items-center gap-2">
        <input
          value={globalFilter}
          onChange={(e) => setGlobalFilter(e.target.value)}
          placeholder={t("dashboard.table.search_all_columns", "Search all columns...")}
          className="h-9 min-w-56 rounded-md border border-border/70 bg-background px-3 text-sm"
        />
        <select className="h-9 rounded-md border border-border/70 bg-background px-3 text-sm" value={pagination.pageSize} onChange={(e) => setPagination((p) => ({ ...p, pageSize: Number(e.target.value) }))}>
          <option value={10}>{t("dashboard.table.rows_10", "10 rows")}</option>
          <option value={20}>{t("dashboard.table.rows_20", "20 rows")}</option>
          <option value={50}>{t("dashboard.table.rows_50", "50 rows")}</option>
        </select>
        <details className="rounded-md border border-border/70 bg-background px-3 py-1.5 text-sm">
          <summary className="cursor-pointer">{t("dashboard.table.columns", "Columns")}</summary>
          <div className="mt-2 space-y-1">
            {table.getAllLeafColumns().map((column) => (
              <label key={column.id} className="flex items-center gap-2">
                <input type="checkbox" checked={column.getIsVisible()} onChange={column.getToggleVisibilityHandler()} />
                <span>{column.id}</span>
              </label>
            ))}
          </div>
        </details>
        {enableExport ? (
          <>
            <button className="h-9 rounded-md border border-border/70 bg-background px-3 text-sm" onClick={exportCsv}>
              {t("dashboard.table.export_csv", "Export CSV")}
            </button>
            <button className="h-9 rounded-md border border-border/70 bg-background px-3 text-sm" onClick={exportXlsx}>
              {t("dashboard.table.export_xlsx", "Export Excel")}
            </button>
          </>
        ) : null}
        {toolbarActions}
      </div>
      {table.getAllLeafColumns().some((column) => column.getCanFilter()) ? (
        <div className="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
          {table.getAllLeafColumns().filter((column) => column.getCanFilter()).map((column) => {
            const key = column.id;
            return (
              <input
                key={key}
                placeholder={`${t("dashboard.table.filter", "Filter")} ${key}`}
                className="h-9 rounded-md border border-border/70 bg-background px-3 text-sm"
                value={(table.getColumn(key)?.getFilterValue() as string) ?? ""}
                onChange={(e) => table.getColumn(key)?.setFilterValue(e.target.value)}
              />
            );
          })}
        </div>
      ) : null}
      <div className="overflow-x-auto rounded-lg border border-border/70 bg-background">
        <table className="min-w-full text-sm">
          <thead className="sticky top-0 z-10 bg-muted">
            {table.getHeaderGroups().map((headerGroup) => (
              <tr key={headerGroup.id}>
                <th className="px-3 py-2 text-start">
                  <input
                    type="checkbox"
                    checked={table.getIsAllPageRowsSelected()}
                    onChange={table.getToggleAllPageRowsSelectedHandler()}
                    aria-label={t("dashboard.common.select_all_rows", "Select all rows on this page")}
                  />
                </th>
                {headerGroup.headers.map((header) => (
                  <th
                    key={header.id}
                    className="cursor-pointer px-3 py-2 text-start text-xs font-semibold uppercase tracking-wide text-muted-foreground"
                    onClick={header.column.getToggleSortingHandler()}
                  >
                    {flexRender(header.column.columnDef.header, header.getContext())}
                  </th>
                ))}
              </tr>
            ))}
          </thead>
          <tbody>
            {table.getRowModel().rows.map((row) => (
              <tr key={row.id} className="border-t border-border/60 transition hover:bg-muted/60">
                <td className="px-3 py-2">
                  <input
                    type="checkbox"
                    checked={row.getIsSelected()}
                    onChange={row.getToggleSelectedHandler()}
                    aria-label={t("dashboard.common.select_row", "Select row")}
                  />
                </td>
                {row.getVisibleCells().map((cell) => (
                  <td key={cell.id} className="px-3 py-2">
                    {flexRender(cell.column.columnDef.cell, cell.getContext())}
                  </td>
                ))}
              </tr>
            ))}
          </tbody>
        </table>
      </div>
      <div className="flex items-center justify-between">
        <button
          className="h-8 rounded-md border border-border/70 bg-background px-3 text-sm disabled:opacity-50"
          onClick={() => table.previousPage()}
          disabled={!table.getCanPreviousPage()}
        >
          {t("dashboard.table.previous", "Previous")}
        </button>
        <span className="text-sm text-muted-foreground">
          {t("dashboard.table.page", "Page")} {table.getState().pagination.pageIndex + 1} {t("dashboard.table.of", "of")} {table.getPageCount() || 1}
        </span>
        <span className="text-xs text-muted-foreground">
          {t("dashboard.table.selected", "Selected")}: {table.getSelectedRowModel().rows.length}
        </span>
        <button
          className="h-8 rounded-md border border-border/70 bg-background px-3 text-sm disabled:opacity-50"
          onClick={() => table.nextPage()}
          disabled={!table.getCanNextPage()}
        >
          {t("dashboard.table.next", "Next")}
        </button>
      </div>
    </div>
  );
}
