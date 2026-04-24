"use client";

import { useEffect, useRef, useState } from "react";
import { Input } from "@/components/ui/input";

const ARABIC_MAP: Record<string, string> = {
  "أ": "a", "إ": "a", "آ": "a", "ا": "a",
  "ب": "b", "ت": "t", "ث": "th",
  "ج": "j", "ح": "h", "خ": "kh",
  "د": "d", "ذ": "dh",
  "ر": "r", "ز": "z",
  "س": "s", "ش": "sh",
  "ص": "s", "ض": "d",
  "ط": "t", "ظ": "z",
  "ع": "a", "غ": "gh",
  "ف": "f", "ق": "q",
  "ك": "k", "ل": "l",
  "م": "m", "ن": "n",
  "ه": "h", "و": "w",
  "ي": "y", "ى": "y",
  "ة": "h",
  "٠": "0", "١": "1", "٢": "2", "٣": "3", "٤": "4",
  "٥": "5", "٦": "6", "٧": "7", "٨": "8", "٩": "9",
};

function transliterateChar(ch: string): string {
  if (ARABIC_MAP[ch]) return ARABIC_MAP[ch];
  return "";
}

export function slugify(input: string): string {
  let result = "";
  for (const ch of input) {
    if (/[a-zA-Z0-9]/.test(ch)) {
      result += ch;
    } else if (ch === " " || ch === "-" || ch === "_") {
      result += "-";
    } else if (ch === "\u0660" || (ch >= "\u0660" && ch <= "\u0669")) {
      result += String.fromCharCode(ch.charCodeAt(0) - 0x0660 + 48);
    } else if (/[\u0600-\u06FF]/.test(ch)) {
      result += transliterateChar(ch);
    }
  }
  result = result.replace(/-+/g, "-").replace(/^-|-$/g, "");
  result = result.toLowerCase();
  return result;
}

interface SlugInputProps {
  value: string;
  onChange: (value: string) => void;
  sourceValue?: string;
  placeholder?: string;
  id?: string;
  required?: boolean;
}

export function SlugInput({ value, onChange, sourceValue, placeholder, id, required }: SlugInputProps) {
  const [isManual, setIsManual] = useState(false);
  const prevSourceRef = useRef(sourceValue);

  useEffect(() => {
    if (sourceValue === undefined) return;
    if (isManual) return;

    const sourceChanged = sourceValue !== prevSourceRef.current;
    prevSourceRef.current = sourceValue;

    if (sourceChanged || value === "") {
      const generated = slugify(sourceValue);
      if (generated !== value) {
        onChange(generated);
      }
    }
  }, [sourceValue, value, onChange, isManual]);

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    setIsManual(true);
    let val = e.target.value;
    val = val.replace(/\s+/g, "-");
    val = val.replace(/[\u0660-\u0669]/g, (d) => String.fromCharCode(d.charCodeAt(0) - 0x0660 + 48));
    let result = "";
    for (const ch of val) {
      if (/[a-zA-Z0-9\-]/.test(ch)) {
        result += ch;
      } else if (/[\u0600-\u06FF]/.test(ch)) {
        result += transliterateChar(ch);
      }
    }
    result = result.replace(/-+/g, "-");
    onChange(result);
  };

  const resetManual = () => {
    setIsManual(false);
    if (sourceValue !== undefined) {
      onChange(slugify(sourceValue));
    }
  };

  return (
    <div className="flex gap-1.5">
      <Input
        id={id}
        type="text"
        value={value}
        onChange={handleChange}
        placeholder={placeholder}
        required={required}
        className="flex-1"
      />
      {isManual && (
        <button
          type="button"
          onClick={resetManual}
          className="shrink-0 text-xs text-muted-foreground underline underline-offset-2 hover:text-foreground"
        >
          Reset
        </button>
      )}
    </div>
  );
}
