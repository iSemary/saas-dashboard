"use client";

import { useEffect, useState, useRef } from "react";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { Loader2 } from "lucide-react";

type EntityOption = {
  id: string;
  name: string;
  raw: Record<string, unknown>;
};

interface EntitySelectorProps {
  value: string;
  onChange: (value: string) => void;
  listFn: () => Promise<unknown[]>;
  optionLabelKey?: string;
  optionValueKey?: string;
  parentKey?: string;
  parentLabelKey?: string;
  placeholder?: string;
  required?: boolean;
  disabled?: boolean;
}

export function EntitySelector({
  value,
  onChange,
  listFn,
  optionLabelKey = "name",
  optionValueKey = "id",
  parentKey,
  parentLabelKey = "name",
  placeholder = "Select...",
  required = false,
  disabled = false,
}: EntitySelectorProps) {
  const [options, setOptions] = useState<EntityOption[]>([]);
  const [loading, setLoading] = useState(true);
  const initialized = useRef(false);

  useEffect(() => {
    if (initialized.current) return;
    initialized.current = true;

    listFn()
      .then((data) => {
        const normalized = (data || []).map((item: unknown) => {
          const typed = item as Record<string, unknown>;
          return {
            id: String(typed[optionValueKey] ?? ""),
            name: String(typed[optionLabelKey] ?? ""),
            raw: typed,
          };
        });
        setOptions(normalized);
      })
      .catch(() => {
        setOptions([]);
      })
      .finally(() => {
        setLoading(false);
      });
  }, [listFn, optionLabelKey, optionValueKey]);

  const getOptionLabel = (option: EntityOption): string => {
    if (!parentKey) return option.name;
    const parent = option.raw[parentKey] as Record<string, unknown> | undefined;
    if (parent && typeof parent === "object") {
      const parentName = parent[parentLabelKey];
      if (typeof parentName === "string" && parentName) {
        return `${option.name}, ${parentName}`;
      }
    }
    return option.name;
  };

  const handleValueChange = (newValue: string | null) => {
    onChange(newValue ?? "");
  };

  const selectedOption = options.find((opt) => opt.id === value);

  return (
    <Select
      value={value || ""}
      onValueChange={handleValueChange}
      disabled={disabled || loading}
    >
      <SelectTrigger className="w-full">
        <SelectValue placeholder={loading ? "Loading..." : placeholder}>
          {selectedOption && getOptionLabel(selectedOption)}
        </SelectValue>
        {loading && <Loader2 className="ml-2 size-4 animate-spin" />}
      </SelectTrigger>
      <SelectContent>
        {!required && (
          <SelectItem value="">— None —</SelectItem>
        )}
        {options.map((option) => (
          <SelectItem key={option.id} value={option.id}>
            {getOptionLabel(option)}
          </SelectItem>
        ))}
      </SelectContent>
    </Select>
  );
}
