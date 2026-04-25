'use client';

import { cn } from '@/lib/utils';

interface NpsQuestionProps {
  value: number;
  onChange: (value: number) => void;
}

export function NpsQuestion({ value, onChange }: NpsQuestionProps) {
  const getLabel = (score: number) => {
    if (score <= 6) return 'Detractor';
    if (score <= 8) return 'Passive';
    return 'Promoter';
  };

  return (
    <div className="space-y-4">
      <div className="flex gap-1">
        {Array.from({ length: 11 }, (_, i) => i).map((score) => (
          <button
            key={score}
            type="button"
            onClick={() => onChange(score)}
            className={cn(
              'w-10 h-10 text-sm font-medium rounded transition-colors',
              value === score
                ? 'bg-primary text-primary-foreground'
                : 'bg-muted hover:bg-muted/80',
              score <= 6 && 'hover:bg-red-100',
              score >= 7 && score <= 8 && 'hover:bg-yellow-100',
              score >= 9 && 'hover:bg-green-100'
            )}
          >
            {score}
          </button>
        ))}
      </div>
      <div className="flex justify-between text-xs text-muted-foreground">
        <span>Not at all likely</span>
        <span>Extremely likely</span>
      </div>
      {value !== undefined && (
        <p className="text-sm font-medium text-center">
          {value} - {getLabel(value)}
        </p>
      )}
    </div>
  );
}
