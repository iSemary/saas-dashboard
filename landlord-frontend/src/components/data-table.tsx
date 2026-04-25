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
import { ReactNode, useState, useMemo, useEffect } from "react";
import { useI18n } from "@/context/i18n-context";
import * as XLSX from "xlsx";
import { FileSpreadsheet, Download, TableProperties, ChevronLeft, ChevronRight, ChevronFirst, ChevronLast, ArrowUpDown, ArrowUp, ArrowDown, Search, Loader2 } from "lucide-react";
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/components/ui/tooltip";
import { Button } from "@/components/ui/button";
import { cn } from "@/lib/utils";

interface TableMeta {
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
}

interface Props<TData> {
  columns: Array<ColumnDef<TData>>;
  data: TData[];
  toolbarActions?: ReactNode;
  enableExport?: boolean;
  searchable?: boolean;
  /** Enable server-side operations (search, sort, pagination handled by backend) */
  serverSide?: boolean;
  /** Total page count (required when serverSide=true) */
  pageCount?: number;
  /** Table metadata from backend pagination (required when serverSide=true) */
  meta?: TableMeta;
  /** Loading state for server-side operations */
  loading?: boolean;
  /** Callback when table params change (search, sort, pagination) */
  onTableChange?: (params: {
    page: number;
    perPage: number;
    search: string;
    sortBy: string | null;
    sortDirection: 'asc' | 'desc';
  }) => void;
}

function SortIcon({ column }: { column: { getIsSorted: () => false | "asc" | "desc" } }) {
  const sort = column.getIsSorted();
  if (sort === "asc") return <ArrowUp className="size-3.5" />;
  if (sort === "desc") return <ArrowDown className="size-3.5" />;
  return <ArrowUpDown className="size-3.5 opacity-40" />;
}

function PaginationNumbers({
  pageIndex,
  pageCount,
  onPageChange,
}: {
  pageIndex: number;
  pageCount: number;
  onPageChange: (page: number) => void;
}) {
  const pages = useMemo(() => {
    const result: (number | string)[] = [];
    const total = pageCount;
    const current = pageIndex;

    if (total <= 7) {
      for (let i = 0; i < total; i++) result.push(i);
    } else {
      result.push(0);
      if (current > 3) result.push("...");
      const start = Math.max(1, current - 1);
      const end = Math.min(total - 2, current + 1);
      for (let i = start; i <= end; i++) result.push(i);
      if (current < total - 4) result.push("...");
      result.push(total - 1);
    }
    return result;
  }, [pageIndex, pageCount]);

  if (pageCount <= 1) return null;

  return (
    <div className="flex items-center gap-1">
      {pages.map((p, i) =>
        p === "..." ? (
          <span key={`ellipsis-${i}`} className="px-2 text-muted-foreground">...</span>
        ) : (
          <button
            key={p}
            onClick={() => onPageChange(p as number)}
            className={cn(
              "h-8 min-w-8 rounded-md px-2 text-sm font-medium transition-colors",
              p === pageIndex
                ? "bg-primary text-primary-foreground"
                : "border border-border/70 bg-background hover:bg-muted"
            )}
          >
            {(p as number) + 1}
          </button>
        )
      )}
    </div>
  );
}

export function DataTable<TData>({
  columns,
  data,
  toolbarActions,
  enableExport = true,
  searchable = true,
  serverSide = false,
  pageCount: propPageCount,
  meta,
  loading = false,
  onTableChange,
}: Props<TData>) {
  const { t } = useI18n();
  const [sorting, setSorting] = useState<SortingState>([]);
  const [globalFilter, setGlobalFilter] = useState("");
  const [columnFilters, setColumnFilters] = useState<ColumnFiltersState>([]);
  const [columnVisibility, setColumnVisibility] = useState<VisibilityState>({});
  const [rowSelection, setRowSelection] = useState<RowSelectionState>({});
  const [pagination, setPagination] = useState<PaginationState>({ pageIndex: 0, pageSize: 10 });

  // Calculate page count for server-side
  const pageCount = serverSide
    ? (propPageCount ?? meta?.last_page ?? 1)
    : Math.ceil(data.length / pagination.pageSize);

  // Client-side filtering (only when not server-side)
  const filteredData = useMemo(() => {
    if (serverSide) return data;
    if (!globalFilter) return data;
    const searchLower = globalFilter.toLowerCase();
    return data.filter((row) => {
      return columns.some((col) => {
        const meta = col.meta as { searchable?: boolean } | undefined;
        if (!meta?.searchable) return false;
        const accessorKey = (col as { accessorKey?: string }).accessorKey;
        if (!accessorKey) return false;
        const value = (row as Record<string, unknown>)[accessorKey];
        return String(value ?? "").toLowerCase().includes(searchLower);
      });
    });
  }, [data, globalFilter, columns, serverSide]);

  const table = useReactTable({
    columns,
    data: filteredData,
    state: { sorting, columnFilters, columnVisibility, rowSelection, pagination },
    onSortingChange: (updater) => {
      const newSorting = typeof updater === 'function' ? updater(sorting) : updater;
      setSorting(newSorting);

      // Notify parent of sort change (server-side)
      if (serverSide && onTableChange) {
        const sortColumn = newSorting[0];
        onTableChange({
          page: pagination.pageIndex + 1,
          perPage: pagination.pageSize,
          search: globalFilter,
          sortBy: sortColumn?.id ?? null,
          sortDirection: sortColumn?.desc ? 'desc' : 'asc',
        });
      }
    },
    onColumnFiltersChange: setColumnFilters,
    onColumnVisibilityChange: setColumnVisibility,
    onRowSelectionChange: setRowSelection,
    onPaginationChange: (updater) => {
      const newPagination = typeof updater === 'function' ? updater(pagination) : updater;
      setPagination(newPagination);

      // Notify parent of pagination change (server-side)
      if (serverSide && onTableChange) {
        onTableChange({
          page: newPagination.pageIndex + 1,
          perPage: newPagination.pageSize,
          search: globalFilter,
          sortBy: sorting[0]?.id ?? null,
          sortDirection: sorting[0]?.desc ? 'desc' : 'asc',
        });
      }
    },
    getCoreRowModel: getCoreRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    getFacetedRowModel: getFacetedRowModel(),
    getFacetedUniqueValues: getFacetedUniqueValues(),
    getSortedRowModel: getSortedRowModel(),
    getPaginationRowModel: serverSide ? undefined : getPaginationRowModel(),
    manualPagination: serverSide,
    pageCount: serverSide ? pageCount : undefined,
    enableRowSelection: true,
  });

  // Handle search changes with debounce for server-side
  useEffect(() => {
    if (!serverSide || !onTableChange) return;

    const timeout = setTimeout(() => {
      onTableChange({
        page: pagination.pageIndex + 1,
        perPage: pagination.pageSize,
        search: globalFilter,
        sortBy: sorting[0]?.id ?? null,
        sortDirection: sorting[0]?.desc ? 'desc' : 'asc',
      });
    }, 300);

    return () => clearTimeout(timeout);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [globalFilter]);
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

  const rowCount = serverSide
    ? (meta?.total ?? data.length)
    : table.getFilteredRowModel().rows.length;
  const totalRows = serverSide
    ? (meta?.total ?? data.length)
    : data.length;

  return (
    <TooltipProvider delay={300}>
      <div className="space-y-3">
        {/* Toolbar */}
        <div className="flex flex-wrap items-center gap-2">
          {searchable && (
            <div className="relative flex-1 min-w-56 max-w-md">
              <Search className="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground" />
              <input
                value={globalFilter}
                onChange={(e) => {
                  setGlobalFilter(e.target.value);
                  setPagination((p) => ({ ...p, pageIndex: 0 }));
                }}
                placeholder={t("dashboard.table.search", "Search...")}
                className="h-9 w-full rounded-md border border-border/70 bg-background pl-9 pr-3 text-sm"
              />
            </div>
          )}

          <div className="flex items-center gap-2 ml-auto">
            {/* Per Page Selector */}
            <select
              className="h-9 rounded-md border border-border/70 bg-background px-3 text-sm"
              value={pagination.pageSize}
              onChange={(e) => {
                const val = e.target.value;
                const newPerPage = val === "all" ? totalRows : Number(val);
                setPagination((p) => ({
                  ...p,
                  pageSize: val === "all" ? totalRows : Number(val),
                  pageIndex: 0,
                }));

                // Notify parent of per-page change (server-side)
                if (serverSide && onTableChange) {
                  onTableChange({
                    page: 1,
                    perPage: newPerPage,
                    search: globalFilter,
                    sortBy: sorting[0]?.id ?? null,
                    sortDirection: sorting[0]?.desc ? 'desc' : 'asc',
                  });
                }
              }}
            >
              <option value={10}>{t("dashboard.table.per_page_10", "10")}</option>
              <option value={25}>{t("dashboard.table.per_page_25", "25")}</option>
              <option value={50}>{t("dashboard.table.per_page_50", "50")}</option>
              <option value={100}>{t("dashboard.table.per_page_100", "100")}</option>
              <option value="all">{t("dashboard.table.per_page_all", "All")}</option>
            </select>

            {/* Columns Dropdown */}
            <Tooltip>
              <TooltipTrigger>
                <details className="rounded-md border border-border/70 bg-background">
                  <summary className="flex cursor-pointer list-none items-center justify-center h-9 w-9 hover:bg-muted rounded-md transition-colors">
                    <TableProperties className="size-4" />
                  </summary>
                  <div className="absolute z-50 mt-1 w-48 rounded-md border border-border/70 bg-popover p-2 shadow-md">
                    <div className="space-y-1">
                      {table.getAllLeafColumns().map((column) => (
                        <label key={column.id} className="flex items-center gap-2 px-2 py-1.5 hover:bg-muted rounded cursor-pointer">
                          <input
                            type="checkbox"
                            checked={column.getIsVisible()}
                            onChange={column.getToggleVisibilityHandler()}
                          />
                          <span className="text-sm">{column.id}</span>
                        </label>
                      ))}
                    </div>
                  </div>
                </details>
              </TooltipTrigger>
              <TooltipContent>{t("dashboard.table.columns", "Columns")}</TooltipContent>
            </Tooltip>

            {/* Export Buttons */}
            {enableExport ? (
              <>
                <Tooltip>
                  <TooltipTrigger>
                    <Button variant="ghost" size="icon" className="h-9 w-9" onClick={exportXlsx}>
                      <FileSpreadsheet className="size-4" />
                    </Button>
                  </TooltipTrigger>
                  <TooltipContent>{t("dashboard.table.export_xlsx", "Export Excel")}</TooltipContent>
                </Tooltip>
                <Tooltip>
                  <TooltipTrigger>
                    <Button variant="ghost" size="icon" className="h-9 w-9" onClick={exportCsv}>
                      <Download className="size-4" />
                    </Button>
                  </TooltipTrigger>
                  <TooltipContent>{t("dashboard.table.export_csv", "Export CSV")}</TooltipContent>
                </Tooltip>
              </>
            ) : null}

            {toolbarActions}
          </div>
        </div>

        {/* Row count info */}
        <div className="flex items-center justify-between text-sm text-muted-foreground">
          <span>
            {t("dashboard.table.showing", "Showing")}{" "}
            {rowCount > 0 ? pagination.pageIndex * pagination.pageSize + 1 : 0}
            {" - "}
            {Math.min((pagination.pageIndex + 1) * pagination.pageSize, rowCount)}
            {" "}{t("dashboard.table.of", "of")}{" "}
            {rowCount} {t("dashboard.table.records", "records")}
          </span>
          {table.getSelectedRowModel().rows.length > 0 && (
            <span>
              {t("dashboard.table.selected", "Selected")}: {table.getSelectedRowModel().rows.length}
            </span>
          )}
        </div>

        {/* Table */}
        <div className="relative overflow-x-auto rounded-lg border border-border/70 bg-background">
          {loading && (
            <div className="absolute inset-0 z-20 flex items-center justify-center bg-background/60 backdrop-blur-[1px]">
              <Loader2 className="size-8 animate-spin text-muted-foreground" />
            </div>
          )}
          <table className="min-w-full text-sm">
            <thead className="sticky top-0 z-10 bg-muted">
              {table.getHeaderGroups().map((headerGroup) => (
                <tr key={headerGroup.id}>
                  <th className="px-3 py-2 text-start w-10">
                    <input
                      type="checkbox"
                      checked={table.getIsAllPageRowsSelected()}
                      onChange={table.getToggleAllPageRowsSelectedHandler()}
                      aria-label={t("dashboard.common.select_all_rows", "Select all rows on this page")}
                    />
                  </th>
                  {headerGroup.headers.map((header) => {
                    const meta = header.column.columnDef.meta as { sortable?: boolean } | undefined;
                    const isSortable = meta?.sortable !== false && header.column.getCanSort();
                    return (
                      <th
                        key={header.id}
                        className={cn(
                          "px-3 py-2 text-start text-xs font-semibold uppercase tracking-wide text-muted-foreground",
                          isSortable && "cursor-pointer select-none hover:bg-muted/80"
                        )}
                        onClick={isSortable ? header.column.getToggleSortingHandler() : undefined}
                      >
                        <div className="flex items-center gap-1.5">
                          {flexRender(header.column.columnDef.header, header.getContext())}
                          {isSortable && <SortIcon column={header.column} />}
                        </div>
                      </th>
                    );
                  })}
                </tr>
              ))}
            </thead>
            <tbody>
              {table.getRowModel().rows.length > 0 ? (
                table.getRowModel().rows.map((row) => (
                  <tr key={row.id} className="border-t border-border/60 transition hover:bg-muted/60">
                    <td className="px-3 py-2 w-10">
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
                ))
              ) : (
                <tr>
                  <td
                    colSpan={table.getVisibleLeafColumns().length + 1}
                    className="px-3 py-12 text-center text-muted-foreground"
                  >
                    <div className="flex flex-col items-center gap-2">
                      <TableProperties className="size-8 opacity-40" />
                      <p>{t("dashboard.table.no_data", "No records found")}</p>
                    </div>
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>

        {/* Pagination */}
        <div className="flex items-center justify-between">
          <div className="flex items-center gap-2">
            <Button
              variant="outline"
              size="icon"
              className="h-8 w-8"
              onClick={() => table.setPageIndex(0)}
              disabled={!table.getCanPreviousPage()}
            >
              <ChevronFirst className="size-4" />
            </Button>
            <Button
              variant="outline"
              size="icon"
              className="h-8 w-8"
              onClick={() => table.previousPage()}
              disabled={!table.getCanPreviousPage()}
            >
              <ChevronLeft className="size-4" />
            </Button>
          </div>

          <PaginationNumbers
            pageIndex={table.getState().pagination.pageIndex}
            pageCount={table.getPageCount()}
            onPageChange={(page) => table.setPageIndex(page)}
          />

          <div className="flex items-center gap-2">
            <Button
              variant="outline"
              size="icon"
              className="h-8 w-8"
              onClick={() => table.nextPage()}
              disabled={!table.getCanNextPage()}
            >
              <ChevronRight className="size-4" />
            </Button>
            <Button
              variant="outline"
              size="icon"
              className="h-8 w-8"
              onClick={() => table.setPageIndex(table.getPageCount() - 1)}
              disabled={!table.getCanNextPage()}
            >
              <ChevronLast className="size-4" />
            </Button>
          </div>
        </div>
      </div>
    </TooltipProvider>
  );
}
