'use client';

import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import { SurveyQuestionOption } from '@/lib/api-survey';

interface DropdownQuestionProps {
  value: string;
  onChange: (value: string) => void;
  options: SurveyQuestionOption[];
}

export function DropdownQuestion({ value, onChange, options }: DropdownQuestionProps) {
  return (
    <Select value={value || ''} onValueChange={(val) => onChange(val || '')}>
      <SelectTrigger>
        <SelectValue placeholder="Select an option" />
      </SelectTrigger>
      <SelectContent>
        {options.map((option) => (
          <SelectItem key={option.id} value={option.value}>
            {option.label}
          </SelectItem>
        ))}
      </SelectContent>
    </Select>
  );
}
