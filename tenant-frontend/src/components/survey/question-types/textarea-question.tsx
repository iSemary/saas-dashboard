'use client';

import { Textarea } from '@/components/ui/textarea';

interface TextareaQuestionProps {
  value: string;
  onChange: (value: string) => void;
  config?: {
    placeholder?: string;
    min_length?: number;
    max_length?: number;
    rows?: number;
  };
}

export function TextareaQuestion({ value, onChange, config }: TextareaQuestionProps) {
  return (
    <Textarea
      value={value || ''}
      onChange={(e) => onChange(e.target.value)}
      placeholder={config?.placeholder || 'Enter your answer'}
      rows={config?.rows || 4}
      minLength={config?.min_length}
      maxLength={config?.max_length}
    />
  );
}
