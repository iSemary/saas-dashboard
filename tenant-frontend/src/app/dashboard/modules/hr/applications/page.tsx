'use client';

import { SimpleCRUDPage } from '@/components/simple-crud-page';
import { RecruitmentApplication, getApplications, applyToJob } from '@/lib/api-hr';
import { ColumnDef } from '@tanstack/react-table';

export default function HrApplicationsPage() {
  return (
    <SimpleCRUDPage<RecruitmentApplication>
      config={{
        titleKey: 'dashboard.hr.applications_title',
        titleFallback: 'Applications',
        subtitleKey: 'dashboard.hr.applications_subtitle',
        subtitleFallback: 'Track job applications and pipeline progress',
        createLabelKey: 'dashboard.hr.applications_create',
        createLabelFallback: 'Submit Application',
        moduleKey: 'hr',
        dashboardHref: '/dashboard/modules/hr',
        fields: [
          { name: 'job_opening_id', label: 'Job Opening ID', type: 'number', required: true },
          { name: 'first_name', label: 'First Name', type: 'text', required: true },
          { name: 'last_name', label: 'Last Name', type: 'text', required: true },
          { name: 'email', label: 'Email', type: 'email', required: true },
          { name: 'phone', label: 'Phone', type: 'text' },
          { name: 'cover_letter', label: 'Cover Letter', type: 'textarea' },
        ],
        listFn: getApplications,
        createFn: applyToJob,
        updateFn: null,
        deleteFn: null,
        columns: () => [
          { accessorKey: 'id', header: 'ID', size: 80 },
          { accessorKey: 'job_opening_id', header: 'Job' },
          { accessorKey: 'candidate_id', header: 'Candidate' },
          { accessorKey: 'pipeline_stage_id', header: 'Stage' },
          { accessorKey: 'status', header: 'Status' },
          { accessorKey: 'applied_at', header: 'Applied At' },
        ] as ColumnDef<RecruitmentApplication>[],
        toForm: () => ({
          job_opening_id: '',
          first_name: '',
          last_name: '',
          email: '',
          phone: '',
          cover_letter: '',
        }),
        fromForm: (form) => ({
          job_opening_id: parseInt(form.job_opening_id),
          first_name: form.first_name,
          last_name: form.last_name,
          email: form.email,
          phone: form.phone || undefined,
          cover_letter: form.cover_letter || undefined,
        }),
      }}
    />
  );
}
