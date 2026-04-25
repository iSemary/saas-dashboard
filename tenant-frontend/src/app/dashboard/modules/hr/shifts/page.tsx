'use client';

import { SimpleCRUDPage } from '@/components/simple-crud-page';
import { getShifts, createShift, updateShift, deleteShift, Shift } from '@/lib/api-hr';
import { ColumnDef } from '@tanstack/react-table';

export default function HrShiftsPage() {
  return (
    <SimpleCRUDPage<Shift>
      config={{
        titleKey: 'hr.shifts.title',
        titleFallback: 'Shifts',
        subtitleKey: 'hr.shifts.subtitle',
        subtitleFallback: 'Manage work shifts and schedules',
        createLabelKey: 'hr.shifts.create',
        createLabelFallback: 'Create Shift',
        moduleKey: 'hr',
        dashboardHref: '/dashboard/modules/hr',
        fields: [
          { name: 'name', label: 'Name', type: 'text', required: true },
          { name: 'start_time', label: 'Start Time', type: 'text', required: true, placeholder: '09:00' },
          { name: 'end_time', label: 'End Time', type: 'text', required: true, placeholder: '17:00' },
          { name: 'break_minutes', label: 'Break (minutes)', type: 'number', required: true },
          { name: 'grace_minutes', label: 'Grace Period (minutes)', type: 'number' },
          { name: 'description', label: 'Description', type: 'textarea' },
          { name: 'is_active', label: 'Active', type: 'select', options: [
            { value: 'true', label: 'Yes' },
            { value: 'false', label: 'No' },
          ]},
        ],
        listFn: getShifts,
        createFn: createShift,
        updateFn: updateShift,
        deleteFn: deleteShift,
        columns: () => [
          { accessorKey: 'id', header: 'ID', size: 80 },
          { accessorKey: 'name', header: 'Name' },
          { accessorKey: 'start_time', header: 'Start' },
          { accessorKey: 'end_time', header: 'End' },
          { accessorKey: 'break_minutes', header: 'Break (min)', size: 100 },
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
        ] as ColumnDef<Shift>[],
        toForm: (row) => ({
          name: row.name || '',
          start_time: row.start_time || '',
          end_time: row.end_time || '',
          break_minutes: row.break_minutes?.toString() || '30',
          grace_minutes: row.grace_minutes?.toString() || '',
          description: row.description || '',
          is_active: row.is_active !== undefined ? String(row.is_active) : 'true',
        }),
        fromForm: (form) => ({
          name: form.name,
          start_time: form.start_time,
          end_time: form.end_time,
          break_minutes: parseInt(form.break_minutes) || 30,
          grace_minutes: form.grace_minutes ? parseInt(form.grace_minutes) : undefined,
          description: form.description,
          is_active: form.is_active === 'true',
        }),
      }}
    />
  );
}
