"use client";

import { useState } from "react";
import PhoneInput from "react-phone-number-input";
import "react-phone-number-input/style.css";
import { cn } from "@/lib/utils";

export interface PhoneInputProps {
  value?: string;
  onChange?: (value: string | undefined) => void;
  placeholder?: string;
  disabled?: boolean;
  // ISO 3166-1 alpha-2 country code (e.g., "US", "GB", "EG", "SA")
  defaultCountry?: string;
  className?: string;
  id?: string;
  name?: string;
}

export function PhoneInputComponent({
  value,
  onChange,
  placeholder,
  disabled,
  defaultCountry = "US",
  className,
  id,
  name,
}: PhoneInputProps) {
  const [internalValue, setInternalValue] = useState<string | undefined>(value);

  const handleChange = (newValue: string | undefined) => {
    setInternalValue(newValue);
    onChange?.(newValue);
  };

  return (
    <div
      className={cn(
        "flex items-center rounded-lg border border-input bg-transparent px-3 py-2 transition-colors focus-within:border-ring focus-within:ring-3 focus-within:ring-ring/50",
        disabled && "cursor-not-allowed opacity-50",
        className
      )}
    >
      <PhoneInput
        id={id}
        name={name}
        value={value ?? internalValue}
        onChange={handleChange}
        placeholder={placeholder}
        disabled={disabled}
        defaultCountry={defaultCountry as "US"}
        international
        countryCallingCodeEditable={false}
        className="phone-input flex-1 [&_.PhoneInputCountry]:mr-2 [&_.PhoneInputInput]:w-full [&_.PhoneInputInput]:bg-transparent [&_.PhoneInputInput]:text-base [&_.PhoneInputInput]:outline-none md:[&_.PhoneInputInput]:text-sm"
      />
    </div>
  );
}

export { PhoneInputComponent as PhoneInput };
