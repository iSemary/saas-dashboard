'use client';

import { Button } from '@/components/ui/button';

interface YesNoQuestionProps {
  value: boolean | null;
  onChange: (value: boolean) => void;
}

export function YesNoQuestion({ value, onChange }: YesNoQuestionProps) {
  return (
    <div className="flex gap-4">
      <Button
        type="button"
        variant={value === true ? 'default' : 'outline'}
        onClick={() => onChange(true)}
        className="flex-1"
      >
        Yes
      </Button>
      <Button
        type="button"
        variant={value === false ? 'default' : 'outline'}
        onClick={() => onChange(false)}
        className="flex-1"
      >
        No
      </Button>
    </div>
  );
}
