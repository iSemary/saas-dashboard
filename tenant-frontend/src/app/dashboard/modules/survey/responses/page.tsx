'use client';

import { useState, useEffect, useCallback } from 'react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { DataTable } from '@/components/ui/data-table';
import { MessageSquare, Eye, Trash2, Loader2, FileText, Download } from 'lucide-react';
import { SurveyResponse, getSurveyResponses, deleteSurveyResponse, getSurveys, Survey } from '@/lib/api-survey';
import Link from 'next/link';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import { toast } from 'sonner';

const STATUS_COLORS: Record<string, string> = {
  started: 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
  completed: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
  partial: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
  disqualified: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
};

export default function ResponsesPage() {
  const [responses, setResponses] = useState<SurveyResponse[]>([]);
  const [surveys, setSurveys] = useState<Survey[]>([]);
  const [selectedSurvey, setSelectedSurvey] = useState<number | null>(null);
  const [loading, setLoading] = useState(false);

  const loadSurveys = useCallback(async () => {
    try {
      const response = await getSurveys();
      setSurveys(response.data);
      if (response.data.length > 0) {
        setSelectedSurvey(response.data[0].id);
      }
    } catch {
      toast.error('Failed to load surveys');
    }
  }, []);

  const loadResponses = useCallback(async () => {
    if (!selectedSurvey) return;
    try {
      setLoading(true);
      const result = await getSurveyResponses(selectedSurvey);
      setResponses(result.data || []);
    } catch {
      toast.error('Failed to load responses');
    } finally {
      setLoading(false);
    }
  }, [selectedSurvey]);

  useEffect(() => {
    loadSurveys();
  }, [loadSurveys]);

  useEffect(() => {
    loadResponses();
  }, [loadResponses]);

  const handleDelete = async (id: number) => {
    if (!confirm('Are you sure you want to delete this response?')) return;
    try {
      await deleteSurveyResponse(id);
      toast.success('Response deleted');
      loadResponses();
    } catch {
      toast.error('Failed to delete response');
    }
  };

  const columns = [
    { key: 'id', header: 'ID', width: '80px' },
    { key: 'respondent_email', header: 'Email', render: (v: string | null) => v || 'Anonymous' },
    { key: 'respondent_name', header: 'Name', render: (v: string | null) => v || '-' },
    {
      key: 'status',
      header: 'Status',
      render: (value: string) => (
        <Badge className={STATUS_COLORS[value] || 'bg-gray-100'}>
          {value}
        </Badge>
      ),
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
      render: (_value: unknown, row: SurveyResponse) => (
        <div className="flex gap-2">
          <Button variant="ghost" size="sm" title="View Details">
            <Eye className="w-4 h-4" />
          </Button>
          <Button
            variant="ghost"
            size="sm"
            className="text-red-600 hover:text-red-700"
            onClick={() => handleDelete(row.id)}
            title="Delete"
          >
            <Trash2 className="w-4 h-4" />
          </Button>
        </div>
      ),
    },
  ];

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
        <div className="flex gap-2">
          <Link href="/dashboard/modules/survey/surveys">
            <Button variant="outline" className="gap-2">
              <FileText className="w-4 h-4" />
              Back to Surveys
            </Button>
          </Link>
          <Button variant="outline" className="gap-2" disabled={!selectedSurvey || responses.length === 0}>
            <Download className="w-4 h-4" />
            Export CSV
          </Button>
        </div>
      </div>

      <Card>
        <CardHeader>
          <div className="flex items-center justify-between">
            <CardTitle>Survey Responses</CardTitle>
            <Select
              value={String(selectedSurvey || '')}
              onValueChange={(v) => setSelectedSurvey(Number(v))}
            >
              <SelectTrigger className="w-[250px]">
                <SelectValue placeholder="Select a survey" />
              </SelectTrigger>
              <SelectContent>
                {surveys.map((s) => (
                  <SelectItem key={s.id} value={String(s.id)}>
                    {s.title}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>
        </CardHeader>
        <CardContent>
          {loading ? (
            <div className="flex justify-center py-8">
              <Loader2 className="w-6 h-6 animate-spin" />
            </div>
          ) : !selectedSurvey ? (
            <p className="text-muted-foreground text-center py-8">
              Please select a survey to view responses
            </p>
          ) : responses.length === 0 ? (
            <p className="text-muted-foreground text-center py-8">
              No responses yet for this survey
            </p>
          ) : (
            <DataTable columns={columns} data={responses} />
          )}
        </CardContent>
      </Card>
    </div>
  );
}
