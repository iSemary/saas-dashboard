'use client';

import { SimpleCRUDPage } from '@/components/simple-crud-page';
import { getPositions, createPosition, updatePosition, deletePosition, Position } from '@/lib/api-hr';
import { ColumnDef } from '@tanstack/react-table';

export default function HrPositionsPage() {
  return (
    <SimpleCRUDPage<Position>
      config={{
        titleKey: 'hr.positions.title',
        titleFallback: 'Positions',
        subtitleKey: 'hr.positions.subtitle',
        subtitleFallback: 'Manage job positions',
        createLabelKey: 'hr.positions.create',
        createLabelFallback: 'Create Position',
        moduleKey: 'hr',
        dashboardHref: '/dashboard/modules/hr',
        fields: [
          { name: 'title', label: 'Title', type: 'text', required: true },
          { name: 'code', label: 'Code', type: 'text' },
          { name: 'department_id', label: 'Department', type: 'number' },
          { name: 'level', label: 'Level', type: 'select', options: [
            { value: 'executive', label: 'Executive' },
            { value: 'director', label: 'Director' },
            { value: 'manager', label: 'Manager' },
            { value: 'senior', label: 'Senior' },
            { value: 'mid', label: 'Mid-Level' },
            { value: 'junior', label: 'Junior' },
            { value: 'intern', label: 'Intern' },
            { value: 'contractor', label: 'Contractor' },
          ]},
          { name: 'min_salary', label: 'Min Salary', type: 'number' },
          { name: 'max_salary', label: 'Max Salary', type: 'number' },
          { name: 'description', label: 'Description', type: 'textarea' },
          { name: 'requirements', label: 'Requirements', type: 'textarea' },
          { name: 'is_active', label: 'Active', type: 'select', options: [
            { value: '1', label: 'Yes' },
            { value: '0', label: 'No' },
          ]},
        ],
        listFn: getPositions,
        createFn: createPosition,
        updateFn: updatePosition,
        deleteFn: deletePosition,
        columns: () => [
          { accessorKey: 'id', header: 'ID', size: 80 },
          { accessorKey: 'title', header: 'Title' },
          { accessorKey: 'code', header: 'Code' },
          { accessorKey: 'level', header: 'Level' },
          {
            accessorKey: 'is_active',
            header: 'Active',
            cell: ({ row }) => {
              const value = row.getValue('is_active') as boolean;
              return (
                <span className={`px-2 py-1 rounded text-xs font-medium ${value ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}`}>
                  {value ? 'Yes' : 'No'}
                </span>
              );
            },
          },
          {
            accessorKey: 'created_at',
            header: 'Created',
            cell: ({ row }) => new Date(row.getValue('created_at')).toLocaleDateString(),
          },
        ] as ColumnDef<Position>[],
        toForm: (row) => ({
          title: row.title || '',
          code: row.code || '',
          department_id: row.department_id?.toString() || '',
          level: row.level || '',
          min_salary: row.min_salary?.toString() || '',
          max_salary: row.max_salary?.toString() || '',
          description: row.description || '',
          requirements: row.requirements || '',
          is_active: row.is_active ? '1' : '0',
        }),
        fromForm: (form) => ({
          title: form.title,
          code: form.code,
          department_id: form.department_id ? parseInt(form.department_id) : undefined,
          level: form.level,
          min_salary: form.min_salary ? parseFloat(form.min_salary) : undefined,
          max_salary: form.max_salary ? parseFloat(form.max_salary) : undefined,
          description: form.description,
          requirements: form.requirements,
          is_active: form.is_active === '1',
        }),
      }}
    />
  );
}
