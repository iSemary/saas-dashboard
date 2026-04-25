'use client';

import { SimpleCRUDPage } from '@/components/simple-crud-page';
import { getEmployees, createEmployee, updateEmployee, deleteEmployee, Employee } from '@/lib/api-hr';
import { ColumnDef } from '@tanstack/react-table';

export default function HrEmployeesPage() {
  return (
    <SimpleCRUDPage<Employee>
      config={{
        titleKey: 'hr.employees.title',
        titleFallback: 'Employees',
        subtitleKey: 'hr.employees.subtitle',
        subtitleFallback: 'Manage your employees directory',
        createLabelKey: 'hr.employees.create',
        createLabelFallback: 'Create Employee',
        moduleKey: 'hr',
        dashboardHref: '/dashboard/modules/hr',
        fields: [
          { name: 'first_name', label: 'First Name', type: 'text', required: true },
          { name: 'middle_name', label: 'Middle Name', type: 'text' },
          { name: 'last_name', label: 'Last Name', type: 'text', required: true },
          { name: 'email', label: 'Email', type: 'email', required: true },
          { name: 'phone', label: 'Phone', type: 'text' },
          { name: 'date_of_birth', label: 'Date of Birth', type: 'text' },
          { name: 'gender', label: 'Gender', type: 'select', options: [
            { value: 'male', label: 'Male' },
            { value: 'female', label: 'Female' },
            { value: 'other', label: 'Other' },
            { value: 'prefer_not_to_say', label: 'Prefer Not To Say' },
          ]},
          { name: 'hire_date', label: 'Hire Date', type: 'text', required: true },
          { name: 'employment_status', label: 'Status', type: 'select', required: true, options: [
            { value: 'active', label: 'Active' },
            { value: 'probation', label: 'Probation' },
            { value: 'on_leave', label: 'On Leave' },
            { value: 'inactive', label: 'Inactive' },
            { value: 'terminated', label: 'Terminated' },
            { value: 'suspended', label: 'Suspended' },
          ]},
          { name: 'employment_type', label: 'Employment Type', type: 'select', required: true, options: [
            { value: 'full_time', label: 'Full Time' },
            { value: 'part_time', label: 'Part Time' },
            { value: 'contract', label: 'Contract' },
            { value: 'intern', label: 'Intern' },
            { value: 'freelance', label: 'Freelance' },
            { value: 'consultant', label: 'Consultant' },
          ]},
          { name: 'department_id', label: 'Department', type: 'number' },
          { name: 'position_id', label: 'Position', type: 'number' },
          { name: 'manager_id', label: 'Manager', type: 'number' },
          { name: 'salary', label: 'Salary', type: 'number' },
          { name: 'currency', label: 'Currency', type: 'text' },
        ],
        listFn: getEmployees,
        createFn: createEmployee,
        updateFn: updateEmployee,
        deleteFn: deleteEmployee,
        columns: () => [
          { accessorKey: 'id', header: 'ID', size: 80 },
          { accessorKey: 'employee_number', header: 'Employee #' },
          { accessorKey: 'full_name', header: 'Full Name' },
          { accessorKey: 'email', header: 'Email' },
          {
            accessorKey: 'employment_status',
            header: 'Status',
            cell: ({ row }) => {
              const value = row.getValue('employment_status') as string;
              const colors: Record<string, string> = {
                active: 'bg-green-100 text-green-800',
                probation: 'bg-yellow-100 text-yellow-800',
                on_leave: 'bg-blue-100 text-blue-800',
                inactive: 'bg-gray-100 text-gray-800',
                terminated: 'bg-red-100 text-red-800',
                suspended: 'bg-orange-100 text-orange-800',
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
        ] as ColumnDef<Employee>[],
        toForm: (row) => ({
          first_name: row.first_name || '',
          middle_name: row.middle_name || '',
          last_name: row.last_name || '',
          email: row.email || '',
          phone: row.phone || '',
          date_of_birth: row.date_of_birth || '',
          gender: row.gender || '',
          hire_date: row.hire_date || '',
          employment_status: row.employment_status || 'active',
          employment_type: row.employment_type || 'full_time',
          department_id: row.department_id?.toString() || '',
          position_id: row.position_id?.toString() || '',
          manager_id: row.manager_id?.toString() || '',
          salary: row.salary?.toString() || '',
          currency: row.currency || 'USD',
        }),
        fromForm: (form) => ({
          first_name: form.first_name,
          middle_name: form.middle_name,
          last_name: form.last_name,
          email: form.email,
          phone: form.phone,
          date_of_birth: form.date_of_birth,
          gender: form.gender,
          hire_date: form.hire_date,
          employment_status: form.employment_status,
          employment_type: form.employment_type,
          department_id: form.department_id ? parseInt(form.department_id) : undefined,
          position_id: form.position_id ? parseInt(form.position_id) : undefined,
          manager_id: form.manager_id ? parseInt(form.manager_id) : undefined,
          salary: form.salary ? parseFloat(form.salary) : undefined,
          currency: form.currency,
        }),
      }}
    />
  );
}
