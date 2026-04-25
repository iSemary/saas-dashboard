'use client';

import { SimpleCRUDPage } from '@/components/simple-crud-page';
import { getDepartments, createDepartment, updateDepartment, deleteDepartment, Department } from '@/lib/api-hr';
import { ColumnDef } from '@tanstack/react-table';

export default function HrDepartmentsPage() {
  return (
    <SimpleCRUDPage<Department>
      config={{
        titleKey: 'dashboard.hr.departments_title',
        titleFallback: 'Departments',
        subtitleKey: 'dashboard.hr.departments_subtitle',
        subtitleFallback: 'Manage your organization departments',
        createLabelKey: 'dashboard.hr.departments_create',
        createLabelFallback: 'Create Department',
        moduleKey: 'hr',
        dashboardHref: '/dashboard/modules/hr',
        fields: [
          { name: 'name', label: 'Name', type: 'text', required: true },
          { name: 'code', label: 'Code', type: 'text' },
          { name: 'parent_id', label: 'Parent Department', type: 'number' },
          { name: 'manager_id', label: 'Manager', type: 'number' },
          { name: 'description', label: 'Description', type: 'textarea' },
          { name: 'status', label: 'Status', type: 'select', required: true, options: [
            { value: 'active', label: 'Active' },
            { value: 'inactive', label: 'Inactive' },
          ]},
        ],
        listFn: getDepartments,
        createFn: createDepartment,
        updateFn: updateDepartment,
        deleteFn: deleteDepartment,
        columns: () => [
          { accessorKey: 'id', header: 'ID', size: 80 },
          { accessorKey: 'name', header: 'Name' },
          { accessorKey: 'code', header: 'Code' },
          {
            accessorKey: 'status',
            header: 'Status',
            cell: ({ row }) => {
              const value = row.getValue('status') as string;
              const colors: Record<string, string> = {
                active: 'bg-green-100 text-green-800',
                inactive: 'bg-gray-100 text-gray-800',
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
        ] as ColumnDef<Department>[],
        toForm: (row) => ({
          name: row.name || '',
          code: row.code || '',
          parent_id: row.parent_id?.toString() || '',
          manager_id: row.manager_id?.toString() || '',
          description: row.description || '',
          status: row.status || 'active',
        }),
        fromForm: (form) => ({
          name: form.name,
          code: form.code,
          parent_id: form.parent_id ? parseInt(form.parent_id) : undefined,
          manager_id: form.manager_id ? parseInt(form.manager_id) : undefined,
          description: form.description,
          status: form.status,
        }),
      }}
    />
  );
}
