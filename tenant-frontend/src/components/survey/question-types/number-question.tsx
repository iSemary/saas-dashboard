'use client';

import { Input } from '@/components/ui/input';

interface NumberQuestionProps {
  value: number;
  onChange: (value: number) => void;
  config?: {
    min?: number;
    max?: number;
    step?: number;
    placeholder?: string;
  };
}

export function NumberQuestion({ value, onChange, config }: NumberQuestionProps) {
  return (
    <Input
      type="number"
      value={value || ''}
      onChange={(e) => onChange(parseFloat(e.target.value))}
      min={config?.min}
      max={config?.max}
      step={config?.step || 1}
      placeholder={config?.placeholder}
    />
  );
}
