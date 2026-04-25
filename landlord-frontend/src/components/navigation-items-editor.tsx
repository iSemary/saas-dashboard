"use client";

import { useCallback } from "react";
import { Plus, Trash2 } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";

type NavItem = {
  key: string;
  label: string;
  route: string;
  icon: string;
};

type NavigationItemsEditorProps = {
  value: string;
  onChange: (value: string) => void;
  disabled?: boolean;
};

const EMPTY_ITEM: NavItem = { key: "", label: "", route: "", icon: "" };

function parseItems(value: string): NavItem[] {
  if (!value) return [];
  try {
    const parsed = JSON.parse(value);
    return Array.isArray(parsed) ? parsed : [];
  } catch {
    return [];
  }
}

function serializeItems(items: NavItem[]): string {
  return JSON.stringify(items);
}

export function NavigationItemsEditor({ value, onChange, disabled }: NavigationItemsEditorProps) {
  const items = parseItems(value);

  const updateItem = useCallback(
    (index: number, field: keyof NavItem, val: string) => {
      const updated = [...items];
      updated[index] = { ...updated[index], [field]: val };
      onChange(serializeItems(updated));
    },
    [items, onChange],
  );

  const addItem = useCallback(() => {
    onChange(serializeItems([...items, { ...EMPTY_ITEM }]));
  }, [items, onChange]);

  const removeItem = useCallback(
    (index: number) => {
      const updated = items.filter((_, i) => i !== index);
      onChange(serializeItems(updated));
    },
    [items, onChange],
  );

  return (
    <div className="space-y-3">
      {items.map((item, index) => (
        <div key={index} className="rounded-lg border bg-background p-3 space-y-2">
          <div className="flex items-center justify-between">
            <span className="text-xs font-medium text-muted-foreground">#{index + 1}</span>
            <Button
              type="button"
              variant="ghost"
              size="sm"
              className="h-7 w-7 p-0 text-destructive"
              onClick={() => removeItem(index)}
              disabled={disabled}
            >
              <Trash2 className="size-3.5" />
            </Button>
          </div>
          <div className="grid grid-cols-2 gap-2">
            <Input
              placeholder="Key (e.g. dashboard)"
              value={item.key}
              onChange={(e) => updateItem(index, "key", e.target.value)}
              disabled={disabled}
              className="h-8 text-xs"
            />
            <Input
              placeholder="Label (e.g. Dashboard)"
              value={item.label}
              onChange={(e) => updateItem(index, "label", e.target.value)}
              disabled={disabled}
              className="h-8 text-xs"
            />
            <Input
              placeholder="Route (e.g. /dashboard/modules/crm)"
              value={item.route}
              onChange={(e) => updateItem(index, "route", e.target.value)}
              disabled={disabled}
              className="h-8 text-xs"
            />
            <Input
              placeholder="Icon (e.g. LayoutDashboard)"
              value={item.icon}
              onChange={(e) => updateItem(index, "icon", e.target.value)}
              disabled={disabled}
              className="h-8 text-xs"
            />
          </div>
        </div>
      ))}
      <Button
        type="button"
        variant="outline"
        size="sm"
        className="h-8 gap-1 w-full"
        onClick={addItem}
        disabled={disabled}
      >
        <Plus className="size-3.5" />
        Add Navigation Item
      </Button>
    </div>
  );
}
