'use client';

import { SimpleCRUDPage } from '@/components/simple-crud-page';
import { TableParams, PaginatedResponse } from '@/lib/tenant-resources';
import { apiClient } from '@/lib/api-client';
import { ColumnDef } from '@tanstack/react-table';

interface PerformanceCycle {
  id: number;
  name: string;
  start_date: string;
  end_date: string;
  review_start_date: string;
  review_end_date: string;
  status: 'draft' | 'active' | 'closed';
  description?: string;
  created_at: string;
}

async function getPerformanceCycles(params?: TableParams): Promise<PaginatedResponse<PerformanceCycle>> {
  const response = await apiClient.get('/tenant/hr/performance-cycles', { params });
  return response.data;
}

async function createPerformanceCycle(data: Omit<PerformanceCycle, 'id' | 'created_at'>) {
  return apiClient.post('/tenant/hr/performance-cycles', data);
}

async function updatePerformanceCycle(id: number, data: Partial<PerformanceCycle>) {
  return apiClient.put(`/tenant/hr/performance-cycles/${id}`, data);
}

async function deletePerformanceCycle(id: number): Promise<void> {
  await apiClient.delete(`/tenant/hr/performance-cycles/${id}`);
}

export default function HrPerformanceCyclesPage() {
  return (
    <SimpleCRUDPage<PerformanceCycle>
      config={{
        titleKey: 'dashboard.hr.performance_cycles_title',
        titleFallback: 'Performance Cycles',
        subtitleKey: 'dashboard.hr.performance_cycles_subtitle',
        subtitleFallback: 'Manage performance review cycles',
        createLabelKey: 'dashboard.hr.performance_cycles_create',
        createLabelFallback: 'Create Cycle',
        moduleKey: 'hr',
        dashboardHref: '/dashboard/modules/hr',
        fields: [
          { name: 'name', label: 'Name', type: 'text', required: true },
          { name: 'start_date', label: 'Start Date', type: 'text', required: true },
          { name: 'end_date', label: 'End Date', type: 'text', required: true },
          { name: 'review_start_date', label: 'Review Start', type: 'text', required: true },
          { name: 'review_end_date', label: 'Review End', type: 'text', required: true },
          { name: 'status', label: 'Status', type: 'select', required: true, options: [
            { value: 'draft', label: 'Draft' },
            { value: 'active', label: 'Active' },
            { value: 'closed', label: 'Closed' },
          ]},
          { name: 'description', label: 'Description', type: 'textarea' },
        ],
        listFn: getPerformanceCycles,
        createFn: createPerformanceCycle,
        updateFn: updatePerformanceCycle,
        deleteFn: deletePerformanceCycle,
        columns: () => [
          { accessorKey: 'name', header: 'Name' },
          { accessorKey: 'start_date', header: 'Period Start' },
          { accessorKey: 'end_date', header: 'Period End' },
          {
            accessorKey: 'status',
            header: 'Status',
            cell: ({ row }) => {
              const value = row.getValue('status') as string;
              const colors: Record<string, string> = {
                draft: 'bg-gray-100 text-gray-800',
                active: 'bg-green-100 text-green-800',
                closed: 'bg-red-100 text-red-800',
              };
              return (
                <span className={`px-2 py-1 rounded text-xs font-medium ${colors[value] || 'bg-gray-100'}`}>
                  {value}
                </span>
              );
            },
          },
        ] as ColumnDef<PerformanceCycle>[],
        toForm: (row) => ({
          name: row.name || '',
          start_date: row.start_date || '',
          end_date: row.end_date || '',
          review_start_date: row.review_start_date || '',
          review_end_date: row.review_end_date || '',
          status: row.status || 'draft',
          description: row.description || '',
        }),
        fromForm: (form) => ({
          name: form.name,
          start_date: form.start_date,
          end_date: form.end_date,
          review_start_date: form.review_start_date,
          review_end_date: form.review_end_date,
          status: form.status,
          description: form.description,
        }),
      }}
    />
  );
}
