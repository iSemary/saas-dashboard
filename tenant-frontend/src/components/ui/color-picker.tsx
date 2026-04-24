"use client";

import { useCallback, useRef, useState } from "react";
import { cn } from "@/lib/utils";

interface ColorPickerProps {
  value: string;
  onChange: (hex: string) => void;
  className?: string;
  disabled?: boolean;
}

export function ColorPicker({ value, onChange, className, disabled }: ColorPickerProps) {
  const inputRef = useRef<HTMLInputElement>(null);
  const [textValue, setTextValue] = useState(value);

  const handleSwatchClick = useCallback(() => {
    inputRef.current?.click();
  }, []);

  const handleInputChange = useCallback(
    (e: React.ChangeEvent<HTMLInputElement>) => {
      const hex = e.target.value;
      onChange(hex);
      setTextValue(hex);
    },
    [onChange]
  );

  const handleTextChange = useCallback(
    (e: React.ChangeEvent<HTMLInputElement>) => {
      const raw = e.target.value.replace(/[^0-9A-Fa-f#]/g, "");
      setTextValue(raw);
      if (/^#[0-9A-Fa-f]{6}$/.test(raw)) {
        onChange(raw);
      }
    },
    [onChange]
  );

  const handleTextBlur = useCallback(() => {
    if (/^#[0-9A-Fa-f]{6}$/.test(textValue)) {
      onChange(textValue);
    } else {
      setTextValue(value);
    }
  }, [textValue, value, onChange]);

  const displayColor = /^#[0-9A-Fa-f]{6}$/.test(value) ? value : "#000000";

  return (
    <div className={cn("flex items-center gap-2", className)}>
      <button
        type="button"
        disabled={disabled}
        onClick={handleSwatchClick}
        className="size-8 shrink-0 rounded-lg border border-input shadow-sm transition-shadow hover:shadow-md focus-visible:ring-3 focus-visible:ring-ring/50 disabled:opacity-50"
        style={{ backgroundColor: displayColor }}
      />
      <input
        ref={inputRef}
        type="color"
        value={displayColor}
        onChange={handleInputChange}
        disabled={disabled}
        className="sr-only"
        tabIndex={-1}
      />
      <input
        type="text"
        value={textValue}
        onChange={handleTextChange}
        onBlur={handleTextBlur}
        disabled={disabled}
        placeholder="#000000"
        maxLength={7}
        className="h-8 w-24 rounded-lg border border-input bg-transparent px-2 py-1 text-sm font-mono outline-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50 disabled:opacity-50 dark:bg-input/30"
      />
    </div>
  );
}
