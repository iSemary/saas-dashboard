'use client';

import { SimpleCRUDPage } from '@/components/simple-crud-page';
import { ColumnDef } from '@tanstack/react-table';
import { JobOpening, getJobOpenings, createJobOpening, updateJobOpening, deleteJobOpening } from '@/lib/api-hr';

export default function HrRecruitmentPage() {
  return (
    <SimpleCRUDPage<JobOpening>
      config={{
        titleKey: 'dashboard.hr.recruitment_title',
        titleFallback: 'Recruitment',
        subtitleKey: 'dashboard.hr.recruitment_subtitle',
        subtitleFallback: 'Manage job openings and hiring pipeline.',
        createLabelKey: 'dashboard.hr.recruitment_create_job',
        createLabelFallback: 'Create Job Opening',
        moduleKey: 'hr',
        dashboardHref: '/dashboard/modules/hr',
        fields: [
          { name: 'title', label: 'Title', type: 'text', required: true },
          { name: 'department_id', label: 'Department ID', type: 'number', required: true },
          { name: 'position_id', label: 'Position ID', type: 'number' },
          { name: 'location', label: 'Location', type: 'text' },
          { name: 'employment_type', label: 'Employment Type', type: 'text', required: true },
          { name: 'type', label: 'Contract Type', type: 'text', required: true },
          { name: 'salary_min', label: 'Minimum Salary', type: 'number' },
          { name: 'salary_max', label: 'Maximum Salary', type: 'number' },
          { name: 'currency', label: 'Currency', type: 'text', required: true },
          {
            name: 'status',
            label: 'Status',
            type: 'select',
            required: true,
            options: [
              { value: 'draft', label: 'Draft' },
              { value: 'published', label: 'Published' },
              { value: 'closed', label: 'Closed' },
              { value: 'on_hold', label: 'On Hold' },
            ],
          },
          { name: 'vacancies', label: 'Vacancies', type: 'number', required: true },
          { name: 'description', label: 'Description', type: 'textarea' },
          { name: 'requirements', label: 'Requirements', type: 'textarea' },
        ],
        listFn: getJobOpenings,
        createFn: createJobOpening,
        updateFn: updateJobOpening,
        deleteFn: deleteJobOpening,
        columns: () => [
          { accessorKey: 'title', header: 'Title' },
          { accessorKey: 'department_id', header: 'Department', size: 120 },
          { accessorKey: 'location', header: 'Location' },
          {
            accessorKey: 'status',
            header: 'Status',
            cell: ({ row }) => {
              const value = row.getValue('status') as string;
              const colors: Record<string, string> = {
                draft: 'bg-gray-100 text-gray-800',
                published: 'bg-green-100 text-green-800',
                closed: 'bg-red-100 text-red-800',
                on_hold: 'bg-yellow-100 text-yellow-800',
              };
              return <span className={`rounded px-2 py-1 text-xs font-medium ${colors[value] || 'bg-gray-100'}`}>{value}</span>;
            },
          },
          { accessorKey: 'vacancies', header: 'Vacancies', size: 100 },
          { accessorKey: 'filled_count', header: 'Filled', size: 100 },
        ] as ColumnDef<JobOpening>[],
        toForm: (row) => ({
          title: row.title || '',
          department_id: row.department_id?.toString() || '',
          position_id: row.position_id?.toString() || '',
          location: row.location || '',
          employment_type: row.employment_type || 'permanent',
          type: row.type || 'full-time',
          salary_min: row.salary_min?.toString() || '',
          salary_max: row.salary_max?.toString() || '',
          currency: row.currency || 'USD',
          status: row.status || 'draft',
          vacancies: row.vacancies?.toString() || '1',
          description: row.description || '',
          requirements: row.requirements || '',
        }),
        fromForm: (form) => ({
          title: form.title,
          department_id: parseInt(form.department_id),
          position_id: form.position_id ? parseInt(form.position_id) : undefined,
          location: form.location || undefined,
          employment_type: form.employment_type,
          type: form.type,
          salary_min: form.salary_min ? parseFloat(form.salary_min) : undefined,
          salary_max: form.salary_max ? parseFloat(form.salary_max) : undefined,
          currency: form.currency || 'USD',
          status: form.status,
          vacancies: form.vacancies ? parseInt(form.vacancies) : 1,
          description: form.description || undefined,
          requirements: form.requirements || undefined,
        }),
      }}
    />
  );
}
