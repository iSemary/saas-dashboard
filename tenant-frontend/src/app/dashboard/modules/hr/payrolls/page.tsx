'use client';

import { SimpleCRUDPage } from '@/components/simple-crud-page';
import { getPayrolls, generatePayroll, deletePayroll, Payroll } from '@/lib/api-hr';
import { ColumnDef } from '@tanstack/react-table';

export default function HrPayrollsPage() {
  return (
    <SimpleCRUDPage<Payroll>
      config={{
        titleKey: 'dashboard.hr.payrolls_title',
        titleFallback: 'Payroll',
        subtitleKey: 'dashboard.hr.payrolls_subtitle',
        subtitleFallback: 'Manage employee payroll and payslips',
        createLabelKey: 'dashboard.hr.payrolls_generate',
        createLabelFallback: 'Generate Payroll',
        moduleKey: 'hr',
        dashboardHref: '/dashboard/modules/hr',
        fields: [
          { name: 'employee_id', label: 'Employee ID', type: 'number', required: true },
          { name: 'pay_period_start', label: 'Period Start', type: 'text', required: true },
          { name: 'pay_period_end', label: 'Period End', type: 'text', required: true },
          { name: 'pay_date', label: 'Pay Date', type: 'text', required: true },
          { name: 'notes', label: 'Notes', type: 'textarea' },
        ],
        listFn: getPayrolls,
        createFn: generatePayroll,
        deleteFn: deletePayroll,
        columns: () => [
          { accessorKey: 'payroll_number', header: 'Payroll #', size: 120 },
          { accessorKey: 'employee_id', header: 'Employee', size: 100 },
          { accessorKey: 'pay_period_start', header: 'From', size: 100 },
          { accessorKey: 'pay_period_end', header: 'To', size: 100 },
          {
            accessorKey: 'status',
            header: 'Status',
            cell: ({ row }) => {
              const value = row.getValue('status') as string;
              const colors: Record<string, string> = {
                draft: 'bg-gray-100 text-gray-800',
                calculated: 'bg-blue-100 text-blue-800',
                approved: 'bg-green-100 text-green-800',
                paid: 'bg-purple-100 text-purple-800',
                cancelled: 'bg-red-100 text-red-800',
              };
              return (
                <span className={`px-2 py-1 rounded text-xs font-medium ${colors[value] || 'bg-gray-100'}`}>
                  {value}
                </span>
              );
            },
          },
          { accessorKey: 'gross_pay', header: 'Gross', size: 100, cell: ({ row }) => 
            `$${Number(row.getValue('gross_pay')).toFixed(2)}` },
          { accessorKey: 'net_pay', header: 'Net', size: 100, cell: ({ row }) => 
            `$${Number(row.getValue('net_pay')).toFixed(2)}` },
        ] as ColumnDef<Payroll>[],
        toForm: (row) => ({
          employee_id: row.employee_id?.toString() || '',
          pay_period_start: row.pay_period_start || '',
          pay_period_end: row.pay_period_end || '',
          pay_date: row.pay_date || '',
          notes: row.notes || '',
        }),
        fromForm: (form) => ({
          employee_id: parseInt(form.employee_id),
          pay_period_start: form.pay_period_start,
          pay_period_end: form.pay_period_end,
          pay_date: form.pay_date,
          notes: form.notes,
        }),
      }}
    />
  );
}
