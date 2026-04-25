'use client';

import { SimpleCRUDPage } from '@/components/simple-crud-page';
import { getLeaveTypes, createLeaveType, updateLeaveType, deleteLeaveType, LeaveType } from '@/lib/api-hr';
import { ColumnDef } from '@tanstack/react-table';

export default function HrLeaveTypesPage() {
  return (
    <SimpleCRUDPage<LeaveType>
      config={{
        titleKey: 'hr.leave_types.title',
        titleFallback: 'Leave Types',
        subtitleKey: 'hr.leave_types.subtitle',
        subtitleFallback: 'Configure leave types and policies',
        createLabelKey: 'hr.leave_types.create',
        createLabelFallback: 'Create Leave Type',
        moduleKey: 'hr',
        dashboardHref: '/dashboard/modules/hr',
        fields: [
          { name: 'name', label: 'Name', type: 'text', required: true },
          { name: 'code', label: 'Code', type: 'text' },
          { name: 'color', label: 'Color', type: 'text', placeholder: '#FF0000' },
          { name: 'is_paid', label: 'Paid Leave', type: 'select', options: [
            { value: 'true', label: 'Yes' },
            { value: 'false', label: 'No' },
          ]},
          { name: 'requires_approval', label: 'Requires Approval', type: 'select', options: [
            { value: 'true', label: 'Yes' },
            { value: 'false', label: 'No' },
          ]},
          { name: 'allow_half_day', label: 'Allow Half Day', type: 'select', options: [
            { value: 'true', label: 'Yes' },
            { value: 'false', label: 'No' },
          ]},
          { name: 'allow_negative_balance', label: 'Allow Negative Balance', type: 'select', options: [
            { value: 'true', label: 'Yes' },
            { value: 'false', label: 'No' },
          ]},
          { name: 'max_consecutive_days', label: 'Max Consecutive Days', type: 'number' },
          { name: 'min_notice_days', label: 'Min Notice Days', type: 'number' },
          { name: 'is_active', label: 'Active', type: 'select', options: [
            { value: 'true', label: 'Yes' },
            { value: 'false', label: 'No' },
          ]},
        ],
        listFn: getLeaveTypes,
        createFn: createLeaveType,
        updateFn: updateLeaveType,
        deleteFn: deleteLeaveType,
        columns: () => [
          { accessorKey: 'id', header: 'ID', size: 80 },
          { accessorKey: 'name', header: 'Name' },
          { accessorKey: 'code', header: 'Code' },
          {
            accessorKey: 'is_paid',
            header: 'Paid',
            cell: ({ row }) => {
              const value = row.getValue('is_paid') as boolean;
              return (
                <span className={`px-2 py-1 rounded text-xs font-medium ${value ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`}>
                  {value ? 'Yes' : 'No'}
                </span>
              );
            },
          },
          {
            accessorKey: 'requires_approval',
            header: 'Approval',
            cell: ({ row }) => {
              const value = row.getValue('requires_approval') as boolean;
              return (
                <span className={`px-2 py-1 rounded text-xs font-medium ${value ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800'}`}>
                  {value ? 'Required' : 'Auto'}
                </span>
              );
            },
          },
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
        ] as ColumnDef<LeaveType>[],
        toForm: (row) => ({
          name: row.name || '',
          code: row.code || '',
          color: row.color || '',
          is_paid: row.is_paid !== undefined ? String(row.is_paid) : 'true',
          requires_approval: row.requires_approval !== undefined ? String(row.requires_approval) : 'true',
          allow_half_day: row.allow_half_day !== undefined ? String(row.allow_half_day) : 'false',
          allow_negative_balance: row.allow_negative_balance !== undefined ? String(row.allow_negative_balance) : 'false',
          max_consecutive_days: row.max_consecutive_days?.toString() || '',
          min_notice_days: row.min_notice_days?.toString() || '',
          is_active: row.is_active !== undefined ? String(row.is_active) : 'true',
        }),
        fromForm: (form) => ({
          name: form.name,
          code: form.code || undefined,
          color: form.color || undefined,
          is_paid: form.is_paid === 'true',
          requires_approval: form.requires_approval === 'true',
          allow_half_day: form.allow_half_day === 'true',
          allow_negative_balance: form.allow_negative_balance === 'true',
          max_consecutive_days: form.max_consecutive_days ? parseInt(form.max_consecutive_days) : undefined,
          min_notice_days: form.min_notice_days ? parseInt(form.min_notice_days) : undefined,
          is_active: form.is_active === 'true',
        }),
      }}
    />
  );
}
