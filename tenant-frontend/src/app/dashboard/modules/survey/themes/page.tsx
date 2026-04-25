'use client';

import { SimpleCRUDPage } from '@/components/simple-crud-page';
import { getSurveyThemes, createSurveyTheme, updateSurveyTheme, deleteSurveyTheme } from '@/lib/api-survey';
import { SurveyTheme } from '@/lib/api-survey';
import { ColumnDef } from '@tanstack/react-table';

export default function ThemesPage() {
  return (
    <SimpleCRUDPage<SurveyTheme>
      config={{
        titleKey: 'survey.themes.title',
        titleFallback: 'Survey Themes',
        subtitleKey: 'survey.themes.subtitle',
        subtitleFallback: 'Customize survey appearance',
        createLabelKey: 'survey.themes.create',
        createLabelFallback: 'Create Theme',
        moduleKey: 'survey',
        dashboardHref: '/dashboard/modules/survey',
        fields: [
          { name: 'name', label: 'Theme Name', type: 'text', required: true },
          { name: 'font_family', label: 'Font Family', type: 'text' },
        ],
        listFn: getSurveyThemes,
        createFn: createSurveyTheme,
        updateFn: updateSurveyTheme,
        deleteFn: deleteSurveyTheme,
        columns: () => [
          { accessorKey: 'id', header: 'ID', size: 80 },
          { accessorKey: 'name', header: 'Theme Name' },
          {
            accessorKey: 'is_system',
            header: 'System',
            cell: ({ row }) => (row.getValue('is_system') ? 'Yes' : 'No'),
          },
          {
            accessorKey: 'colors',
            header: 'Primary Color',
            cell: ({ row }) => {
              const colors = row.getValue('colors') as Record<string, string> | undefined;
              const color = colors?.primary || '#6366f1';
              return (
                <div className="flex items-center gap-2">
                  <div className="w-6 h-6 rounded border" style={{ backgroundColor: color }} />
                  <span className="text-xs text-muted-foreground">{color}</span>
                </div>
              );
            },
          },
        ] as ColumnDef<SurveyTheme>[],
        toForm: (row) => ({
          name: row.name || '',
          font_family: row.font_family || '',
        }),
        fromForm: (form) => ({
          name: form.name,
          font_family: form.font_family,
        }),
      }}
    />
  );
}
