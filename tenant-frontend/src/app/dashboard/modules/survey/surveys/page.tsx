'use client';

import { SimpleCRUDPage } from '@/components/simple-crud-page';
import { getSurveys, createSurvey, updateSurvey, deleteSurvey } from '@/lib/api-survey';
import { Survey } from '@/lib/api-survey';
import { ColumnDef } from '@tanstack/react-table';

export default function SurveysPage() {
  return (
    <SimpleCRUDPage<Survey>
      config={{
        titleKey: 'survey.title',
        titleFallback: 'Surveys',
        subtitleKey: 'survey.subtitle',
        subtitleFallback: 'Manage your surveys',
        createLabelKey: 'survey.create',
        createLabelFallback: 'Create Survey',
        moduleKey: 'survey',
        dashboardHref: '/dashboard/modules/survey',
        fields: [
          { name: 'title', label: 'Title', type: 'text', required: true },
          { name: 'description', label: 'Description', type: 'textarea' },
        ],
        listFn: getSurveys,
        createFn: createSurvey,
        updateFn: updateSurvey,
        deleteFn: deleteSurvey,
        columns: () => [
          { accessorKey: 'id', header: 'ID', size: 80 },
          { accessorKey: 'title', header: 'Title' },
          {
            accessorKey: 'status',
            header: 'Status',
            cell: ({ row }) => {
              const value = row.getValue('status') as string;
              const colors: Record<string, string> = {
                draft: 'bg-yellow-100 text-yellow-800',
                active: 'bg-green-100 text-green-800',
                paused: 'bg-orange-100 text-orange-800',
                closed: 'bg-gray-100 text-gray-800',
                archived: 'bg-red-100 text-red-800',
              };
              return (
                <span className={`px-2 py-1 rounded text-xs font-medium ${colors[value] || 'bg-gray-100'}`}>
                  {value}
                </span>
              );
            },
          },
          {
            accessorKey: 'created_at',
            header: 'Created',
            cell: ({ row }) => new Date(row.getValue('created_at')).toLocaleDateString(),
          },
        ] as ColumnDef<Survey>[],
        toForm: (row) => ({
          title: row.title || '',
          description: row.description || '',
        }),
        fromForm: (form) => ({
          title: form.title,
          description: form.description,
        }),
      }}
    />
  );
}
