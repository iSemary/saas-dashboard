'use client';

import { useState, useEffect } from 'react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { DataTable } from '@/components/ui/data-table';
import { MessageSquare, Eye, Trash2 } from 'lucide-react';
import { SurveyResponse } from '@/lib/api-survey';
import Link from 'next/link';

const columns = [
  { key: 'id', header: 'ID', width: '80px' },
  { key: 'respondent_email', header: 'Email', render: (v: string | null) => v || 'Anonymous' },
  { key: 'respondent_name', header: 'Name', render: (v: string | null) => v || '-' },
  {
    key: 'status',
    header: 'Status',
    render: (value: string) => {
      const colors: Record<string, string> = {
        started: 'bg-blue-100 text-blue-800',
        completed: 'bg-green-100 text-green-800',
        partial: 'bg-yellow-100 text-yellow-800',
        disqualified: 'bg-red-100 text-red-800',
      };
      return (
        <span className={`px-2 py-1 rounded text-xs font-medium ${colors[value] || 'bg-gray-100'}`}>
          {value}
        </span>
      );
    },
  },
  {
    key: 'score',
    header: 'Score',
    render: (v: number | null, row: SurveyResponse) =>
      v !== null && v !== undefined ? `${v}/${row.max_score || '-'}` : '-',
  },
  {
    key: 'completed_at',
    header: 'Completed',
    render: (v: string | null) => v ? new Date(v).toLocaleDateString() : '-',
  },
  {
    key: 'actions',
    header: 'Actions',
    render: (_value: unknown, _row: SurveyResponse) => (
      <div className="flex gap-2">
        <Button variant="ghost" size="sm">
          <Eye className="w-4 h-4" />
        </Button>
        <Button variant="ghost" size="sm" className="text-red-600">
          <Trash2 className="w-4 h-4" />
        </Button>
      </div>
    ),
  },
];

export default function ResponsesPage() {
  const [responses, setResponses] = useState<SurveyResponse[]>([]);

  const loadResponses = async () => {
    try {
      // This would need a surveyId param in real implementation
      // For now showing all responses across surveys
      setResponses([]);
    } catch (error) {
      console.error('Failed to load responses:', error);
    }
  };

  useEffect(() => {
    loadResponses();
  }, []);

  return (
    <div className="p-6 space-y-6">
      <div className="flex justify-between items-center">
        <div>
          <h1 className="text-3xl font-bold flex items-center gap-2">
            <MessageSquare className="w-8 h-8" />
            Survey Responses
          </h1>
          <p className="text-muted-foreground mt-1">
            View and manage survey responses
          </p>
        </div>
        <Link href="/dashboard/modules/survey/surveys">
          <Button variant="outline">Back to Surveys</Button>
        </Link>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>All Responses</CardTitle>
        </CardHeader>
        <CardContent>
          {responses.length === 0 ? (
            <p className="text-muted-foreground text-center py-8">
              Select a survey to view its responses
            </p>
          ) : (
            <DataTable columns={columns} data={responses} />
          )}
        </CardContent>
      </Card>
    </div>
  );
}
