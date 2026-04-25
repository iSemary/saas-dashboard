'use client';

import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { Label } from '@/components/ui/label';
import { SurveyQuestionOption } from '@/lib/api-survey';

interface MultipleChoiceQuestionProps {
  value: string;
  onChange: (value: string) => void;
  options: SurveyQuestionOption[];
}

export function MultipleChoiceQuestion({ value, onChange, options }: MultipleChoiceQuestionProps) {
  return (
    <RadioGroup value={value || ''} onValueChange={(val) => onChange(String(val))} className="space-y-2">
      {options.map((option) => (
        <div key={option.id} className="flex items-center space-x-2">
          <RadioGroupItem value={option.value} id={`option-${option.id}`} />
          <Label htmlFor={`option-${option.id}`} className="cursor-pointer">
            {option.label}
          </Label>
        </div>
      ))}
    </RadioGroup>
  );
}
