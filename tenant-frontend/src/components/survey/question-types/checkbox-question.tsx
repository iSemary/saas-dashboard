'use client';

import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';
import { SurveyQuestionOption } from '@/lib/api-survey';

interface CheckboxQuestionProps {
  value: string[];
  onChange: (value: string[]) => void;
  options: SurveyQuestionOption[];
}

export function CheckboxQuestion({ value = [], onChange, options }: CheckboxQuestionProps) {
  const handleToggle = (optionValue: string) => {
    if (value.includes(optionValue)) {
      onChange(value.filter((v) => v !== optionValue));
    } else {
      onChange([...value, optionValue]);
    }
  };

  return (
    <div className="space-y-2">
      {options.map((option) => (
        <div key={option.id} className="flex items-center space-x-2">
          <Checkbox
            id={`option-${option.id}`}
            checked={value.includes(option.value)}
            onCheckedChange={() => handleToggle(option.value)}
          />
          <Label htmlFor={`option-${option.id}`} className="cursor-pointer">
            {option.label}
          </Label>
        </div>
      ))}
    </div>
  );
}
