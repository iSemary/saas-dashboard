'use client';

import { useEffect, useState, useCallback } from 'react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Copy, FileText } from 'lucide-react';
import { getSurveyTemplates, createSurveyFromTemplate, SurveyTemplate } from '@/lib/api-survey';
import Link from 'next/link';

export default function TemplatesPage() {
  const [templates, setTemplates] = useState<SurveyTemplate[]>([]);
  const [loading, setLoading] = useState(true);

  const loadTemplates = useCallback(async () => {
    try {
      const data = await getSurveyTemplates();
      setTemplates(data);
    } catch (error) {
      console.error('Failed to load templates:', error);
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    loadTemplates();
  }, [loadTemplates]);

  const handleUseTemplate = async (id: number) => {
    try {
      await createSurveyFromTemplate(id);
      // Redirect to surveys page
      if (typeof window !== 'undefined') {
        window.location.href = '/dashboard/modules/survey/surveys';
      }
    } catch (error) {
      console.error('Failed to create survey from template:', error);
    }
  };

  if (loading) {
    return (
      <div className="p-6">
        <div className="animate-pulse space-y-4">
          <div className="h-8 bg-gray-200 rounded w-1/4"></div>
          <div className="grid grid-cols-3 gap-4">
            {[...Array(6)].map((_, i) => (
              <div key={i} className="h-40 bg-gray-200 rounded"></div>
            ))}
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="p-6 space-y-6">
      {/* Header */}
      <div className="flex justify-between items-center">
        <div>
          <h1 className="text-3xl font-bold flex items-center gap-2">
            <Copy className="w-8 h-8" />
            Survey Templates
          </h1>
          <p className="text-muted-foreground mt-1">
            Start with a pre-built template to save time
          </p>
        </div>
        <Link href="/dashboard/modules/survey/surveys">
          <Button variant="outline" className="gap-2">
            <FileText className="w-4 h-4" />
            Back to Surveys
          </Button>
        </Link>
      </div>

      {/* Templates Grid */}
      <div className="grid grid-cols-3 gap-4">
        {templates.map((template) => (
          <Card key={template.id} className="flex flex-col">
            <CardHeader>
              <CardTitle>{template.name}</CardTitle>
              <CardDescription>{template.description}</CardDescription>
            </CardHeader>
            <CardContent className="flex-1 flex flex-col justify-end">
              <div className="flex items-center justify-between">
                <span className="text-sm text-muted- capitalize">
                  {template.category.replace('_', ' ')}
                </span>
                <Button
                  size="sm"
                  onClick={() => handleUseTemplate(template.id)}
                >
                  Use Template
                </Button>
              </div>
            </CardContent>
          </Card>
        ))}
      </div>
    </div>
  );
}
