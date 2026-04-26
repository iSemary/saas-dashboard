"use client";

import { useCallback, useEffect, useRef, useState } from "react";
import { Responsive, WidthProvider } from "react-grid-layout/legacy";
import type { ResponsiveLayouts, LayoutItem, Layout } from "react-grid-layout";
import { GripVertical, RotateCcw } from "lucide-react";
import api from "@/lib/api";

const ResponsiveGridLayout = WidthProvider(Responsive);

const DEBOUNCE_MS = 600;

interface DraggableDashboardGridProps {
  storageKey: string;
  defaultLayouts: ResponsiveLayouts;
  children: React.ReactElement[];
}

function mergeLayouts(saved: ResponsiveLayouts, defaults: ResponsiveLayouts): ResponsiveLayouts {
  const merged: ResponsiveLayouts = {};
  const breakpoints = Object.keys(defaults) as string[];
  for (const bp of breakpoints) {
    const defaultItems: LayoutItem[] = [...(defaults[bp] ?? [])];
    const savedItems: LayoutItem[] = [...(saved[bp] ?? [])];
    const savedMap = new Map(savedItems.map((l) => [l.i, l]));
    const mergedItems: LayoutItem[] = defaultItems.map((def) => savedMap.get(def.i) ?? def);
    const knownIds = new Set(defaultItems.map((d) => d.i));
    const extras = savedItems.filter((s) => !knownIds.has(s.i));
    merged[bp] = [...mergedItems, ...extras];
  }
  return merged;
}

export default function DraggableDashboardGrid({
  storageKey,
  defaultLayouts,
  children,
}: DraggableDashboardGridProps) {
  const [mounted, setMounted] = useState(false);
  const [layouts, setLayouts] = useState<ResponsiveLayouts>(defaultLayouts);
  const [editMode, setEditMode] = useState(false);
  const debounceRef = useRef<ReturnType<typeof setTimeout> | null>(null);
  const pendingRef = useRef<ResponsiveLayouts>(defaultLayouts);

  useEffect(() => {
    setMounted(true);
  }, []);

  useEffect(() => {
    const localRaw = window.localStorage.getItem(storageKey);
    if (localRaw) {
      try {
        const parsed = JSON.parse(localRaw) as ResponsiveLayouts;
        setLayouts(mergeLayouts(parsed, defaultLayouts));
      } catch {
        // ignore
      }
    }

    api
      .get<string | null>(`/user-meta/${storageKey}`)
      .then((res) => {
        const raw = res.data;
        if (raw) {
          const parsed = typeof raw === "string" ? (JSON.parse(raw) as ResponsiveLayouts) : (raw as ResponsiveLayouts);
          const merged = mergeLayouts(parsed, defaultLayouts);
          setLayouts(merged);
          window.localStorage.setItem(storageKey, JSON.stringify(merged));
        }
      })
      .catch(() => {});
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [storageKey]);

  const saveLayouts = useCallback(
    (newLayouts: ResponsiveLayouts) => {
      pendingRef.current = newLayouts;
      window.localStorage.setItem(storageKey, JSON.stringify(newLayouts));
      if (debounceRef.current) clearTimeout(debounceRef.current);
      debounceRef.current = setTimeout(() => {
        api
          .post("/user-meta", { key: storageKey, value: JSON.stringify(pendingRef.current) })
          .catch(() => {});
      }, DEBOUNCE_MS);
    },
    [storageKey]
  );

  const handleLayoutChange = useCallback(
    (_: Layout, allLayouts: ResponsiveLayouts) => {
      setLayouts(allLayouts);
      saveLayouts(allLayouts);
      window.dispatchEvent(new Event("resize"));
    },
    [saveLayouts]
  );

  const handleReset = useCallback(() => {
    setLayouts(defaultLayouts);
    window.localStorage.removeItem(storageKey);
    api.delete(`/user-meta/${storageKey}`).catch(() => {});
  }, [defaultLayouts, storageKey]);

  if (!mounted) {
    return (
      <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
        {children}
      </div>
    );
  }

  return (
    <div className="space-y-2">
      <div className="flex items-center justify-end gap-2">
        <button
          onClick={handleReset}
          className="inline-flex items-center gap-1 rounded-md px-2 py-1 text-xs text-muted-foreground hover:text-foreground hover:bg-muted transition-colors"
          title="Reset layout"
        >
          <RotateCcw className="size-3" />
          Reset
        </button>
        <button
          onClick={() => setEditMode((v) => !v)}
          className={`inline-flex items-center gap-1 rounded-md px-2 py-1 text-xs transition-colors ${
            editMode
              ? "bg-primary text-primary-foreground hover:bg-primary/90"
              : "text-muted-foreground hover:text-foreground hover:bg-muted"
          }`}
        >
          <GripVertical className="size-3" />
          {editMode ? "Done" : "Edit Layout"}
        </button>
      </div>
      <div dir="ltr">
        <ResponsiveGridLayout
          layouts={layouts}
          breakpoints={{ lg: 1200, md: 996, sm: 768, xs: 480 }}
          cols={{ lg: 12, md: 12, sm: 6, xs: 4 }}
          rowHeight={80}
          isDraggable={editMode}
          isResizable={editMode}
          onLayoutChange={handleLayoutChange}
          draggableHandle=".drag-handle"
          containerPadding={[0, 0]}
          margin={[12, 12]}
        >
          {children.map((child) => (
            <div key={child.key} className="relative group">
              {editMode && (
                <div className="drag-handle absolute top-2 right-2 z-10 cursor-grab opacity-0 group-hover:opacity-100 transition-opacity rounded p-0.5 bg-background/80 shadow-sm">
                  <GripVertical className="size-4 text-muted-foreground" />
                </div>
              )}
              <div className={`h-full w-full ${editMode ? "ring-2 ring-primary/30 rounded-xl" : ""}`}>
                {child}
              </div>
            </div>
          ))}
        </ResponsiveGridLayout>
      </div>
    </div>
  );
}
