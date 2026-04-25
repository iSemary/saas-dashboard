'use client';

import { SimpleCRUDPage } from '@/components/simple-crud-page';
import { getHolidays, createHoliday, updateHoliday, deleteHoliday, Holiday } from '@/lib/api-hr';
import { ColumnDef } from '@tanstack/react-table';

export default function HrHolidaysPage() {
  const createHolidayAdapter = (payload: Record<string, unknown>) =>
    createHoliday(payload as Omit<Holiday, 'id' | 'created_at' | 'updated_at'>);
  const updateHolidayAdapter = (id: number, payload: Record<string, unknown>) =>
    updateHoliday(id, payload as Partial<Holiday>);

  return (
    <SimpleCRUDPage<Holiday>
      config={{
        titleKey: 'dashboard.hr.holidays_title',
        titleFallback: 'Holidays',
        subtitleKey: 'dashboard.hr.holidays_subtitle',
        subtitleFallback: 'Manage company holidays',
        createLabelKey: 'dashboard.hr.holidays_create',
        createLabelFallback: 'Add Holiday',
        moduleKey: 'hr',
        dashboardHref: '/dashboard/modules/hr',
        fields: [
          { name: 'name', label: 'Name', type: 'text', required: true },
          { name: 'date', label: 'Date', type: 'text', required: true },
          { name: 'country', label: 'Country', type: 'text' },
          { name: 'is_recurring', label: 'Recurring Yearly', type: 'select', options: [
            { value: 'true', label: 'Yes' },
            { value: 'false', label: 'No' },
          ]},
          { name: 'applies_to_all_departments', label: 'All Departments', type: 'select', options: [
            { value: 'true', label: 'Yes' },
            { value: 'false', label: 'No' },
          ]},
        ],
        listFn: getHolidays,
        createFn: createHolidayAdapter,
        updateFn: updateHolidayAdapter,
        deleteFn: deleteHoliday,
        columns: () => [
          { accessorKey: 'id', header: 'ID', size: 80 },
          { accessorKey: 'name', header: 'Name' },
          { accessorKey: 'date', header: 'Date' },
          { accessorKey: 'country', header: 'Country' },
          {
            accessorKey: 'is_recurring',
            header: 'Recurring',
            cell: ({ row }) => {
              const value = row.getValue('is_recurring') as boolean;
              return (
                <span className={`px-2 py-1 rounded text-xs font-medium ${value ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'}`}>
                  {value ? 'Yearly' : 'Once'}
                </span>
              );
            },
          },
          {
            accessorKey: 'applies_to_all_departments',
            header: 'Scope',
            cell: ({ row }) => {
              const value = row.getValue('applies_to_all_departments') as boolean;
              return (
                <span className={`px-2 py-1 rounded text-xs font-medium ${value ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'}`}>
                  {value ? 'All Depts' : 'Selected'}
                </span>
              );
            },
          },
        ] as ColumnDef<Holiday>[],
        toForm: (row) => ({
          name: row.name || '',
          date: row.date || '',
          country: row.country || '',
          is_recurring: row.is_recurring !== undefined ? String(row.is_recurring) : 'false',
          applies_to_all_departments: row.applies_to_all_departments !== undefined ? String(row.applies_to_all_departments) : 'true',
        }),
        fromForm: (form) => ({
          name: form.name,
          date: form.date,
          country: form.country || undefined,
          is_recurring: form.is_recurring === 'true',
          applies_to_all_departments: form.applies_to_all_departments === 'true',
        }),
      }}
    />
  );
}
