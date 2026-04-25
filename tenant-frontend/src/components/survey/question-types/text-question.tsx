'use client';

import { Input } from '@/components/ui/input';

interface TextQuestionProps {
  value: string;
  onChange: (value: string) => void;
  config?: {
    placeholder?: string;
    min_length?: number;
    max_length?: number;
  };
}

export function TextQuestion({ value, onChange, config }: TextQuestionProps) {
  return (
    <Input
      type="text"
      value={value || ''}
      onChange={(e) => onChange(e.target.value)}
      placeholder={config?.placeholder || 'Enter your answer'}
      minLength={config?.min_length}
      maxLength={config?.max_length}
    />
  );
}
