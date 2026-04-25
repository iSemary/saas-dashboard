'use client';

import { Input } from '@/components/ui/input';

interface EmailQuestionProps {
  value: string;
  onChange: (value: string) => void;
}

export function EmailQuestion({ value, onChange }: EmailQuestionProps) {
  return (
    <Input
      type="email"
      value={value || ''}
      onChange={(e) => onChange(e.target.value)}
      placeholder="Enter your email"
    />
  );
}
