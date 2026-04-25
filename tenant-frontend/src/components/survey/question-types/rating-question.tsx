'use client';

import { Star } from 'lucide-react';
import { useState } from 'react';

interface RatingQuestionProps {
  value: number;
  onChange: (value: number) => void;
  config?: {
    max_stars?: number;
    show_labels?: boolean;
  };
}

export function RatingQuestion({ value, onChange, config }: RatingQuestionProps) {
  const maxStars = config?.max_stars || 5;
  const [hoverRating, setHoverRating] = useState(0);

  return (
    <div className="flex items-center gap-1">
      {Array.from({ length: maxStars }, (_, i) => i + 1).map((star) => (
        <button
          key={star}
          type="button"
          onClick={() => onChange(star)}
          onMouseEnter={() => setHoverRating(star)}
          onMouseLeave={() => setHoverRating(0)}
          className="p-1 focus:outline-none"
        >
          <Star
            className={`w-8 h-8 transition-colors ${
              star <= (hoverRating || value)
                ? 'fill-yellow-400 text-yellow-400'
                : 'text-gray-300'
            }`}
          />
        </button>
      ))}
      {config?.show_labels && value > 0 && (
        <span className="ml-2 text-sm text-muted-foreground">{value} / {maxStars}</span>
      )}
    </div>
  );
}
