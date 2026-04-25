'use client';

import { SimpleCRUDPage } from '@/components/simple-crud-page';
import { Candidate, getCandidates, createCandidate, updateCandidate, deleteCandidate } from '@/lib/api-hr';
import { ColumnDef } from '@tanstack/react-table';

export default function HrCandidatesPage() {
  return (
    <SimpleCRUDPage<Candidate>
      config={{
        titleKey: 'dashboard.hr.candidates_title',
        titleFallback: 'Candidates',
        subtitleKey: 'dashboard.hr.candidates_subtitle',
        subtitleFallback: 'Manage candidate talent pool',
        createLabelKey: 'dashboard.hr.candidates_create',
        createLabelFallback: 'Add Candidate',
        moduleKey: 'hr',
        dashboardHref: '/dashboard/modules/hr',
        fields: [
          { name: 'first_name', label: 'First Name', type: 'text', required: true },
          { name: 'last_name', label: 'Last Name', type: 'text', required: true },
          { name: 'email', label: 'Email', type: 'email', required: true },
          { name: 'phone', label: 'Phone', type: 'text' },
          { name: 'current_title', label: 'Current Title', type: 'text' },
          { name: 'current_company', label: 'Current Company', type: 'text' },
          { name: 'source', label: 'Source', type: 'text' },
        ],
        listFn: getCandidates,
        createFn: createCandidate,
        updateFn: updateCandidate,
        deleteFn: deleteCandidate,
        columns: () => [
          { accessorKey: 'first_name', header: 'First Name' },
          { accessorKey: 'last_name', header: 'Last Name' },
          { accessorKey: 'email', header: 'Email' },
          { accessorKey: 'current_title', header: 'Title' },
          { accessorKey: 'source', header: 'Source' },
        ] as ColumnDef<Candidate>[],
        toForm: (row) => ({
          first_name: row.first_name || '',
          last_name: row.last_name || '',
          email: row.email || '',
          phone: row.phone || '',
          current_title: row.current_title || '',
          current_company: row.current_company || '',
          source: row.source || '',
        }),
        fromForm: (form) => ({
          first_name: form.first_name,
          last_name: form.last_name,
          email: form.email,
          phone: form.phone,
          current_title: form.current_title,
          current_company: form.current_company,
          source: form.source,
        }),
      }}
    />
  );
}
