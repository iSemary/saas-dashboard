'use client';

import { useState } from 'react';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Upload, X } from 'lucide-react';

interface FileUploadQuestionProps {
  value: File | null;
  onChange: (value: File | null) => void;
  config?: {
    accepted_types?: string[];
    max_size_mb?: number;
  };
}

export function FileUploadQuestion({ value, onChange, config }: FileUploadQuestionProps) {
  const [preview, setPreview] = useState<string | null>(null);

  const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0] || null;
    onChange(file);
    
    if (file && file.type.startsWith('image/')) {
      const reader = new FileReader();
      reader.onloadend = () => setPreview(reader.result as string);
      reader.readAsDataURL(file);
    } else {
      setPreview(null);
    }
  };

  const handleRemove = () => {
    onChange(null);
    setPreview(null);
  };

  const accept = config?.accepted_types?.join(',') || '*';

  return (
    <div className="space-y-4">
      {!value ? (
        <div className="border-2 border-dashed border-muted-foreground/25 rounded-lg p-8 text-center">
          <Upload className="w-8 h-8 mx-auto mb-4 text-muted-foreground" />
          <Input
            type="file"
            accept={accept}
            onChange={handleFileChange}
            className="hidden"
            id="file-upload"
          />
          <label htmlFor="file-upload">
            <Button type="button" variant="outline" className="cursor-pointer">
              Choose File
            </Button>
          </label>
          {config?.max_size_mb && (
            <p className="text-xs text-muted-foreground mt-2">
              Max size: {config.max_size_mb}MB
            </p>
          )}
        </div>
      ) : (
        <div className="flex items-center gap-4 p-4 border rounded-lg">
          {preview ? (
            <img src={preview} alt="Preview" className="w-16 h-16 object-cover rounded" />
          ) : (
            <div className="w-16 h-16 bg-muted rounded flex items-center justify-center">
              <Upload className="w-6 h-6 text-muted-foreground" />
            </div>
          )}
          <div className="flex-1 min-w-0">
            <p className="font-medium truncate">{value.name}</p>
            <p className="text-sm text-muted-foreground">
              {(value.size / 1024 / 1024).toFixed(2)} MB
            </p>
          </div>
          <Button type="button" variant="ghost" size="sm" onClick={handleRemove}>
            <X className="w-4 h-4" />
          </Button>
        </div>
      )}
    </div>
  );
}
