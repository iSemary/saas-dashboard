'use client';

import { SimpleCRUDPage } from '@/components/simple-crud-page';
import { RecruitmentInterview, getInterviews, scheduleInterview } from '@/lib/api-hr';
import { ColumnDef } from '@tanstack/react-table';

export default function HrInterviewsPage() {
  return (
    <SimpleCRUDPage<RecruitmentInterview>
      config={{
        titleKey: 'dashboard.hr.interviews_title',
        titleFallback: 'Interviews',
        subtitleKey: 'dashboard.hr.interviews_subtitle',
        subtitleFallback: 'Schedule and monitor candidate interviews',
        createLabelKey: 'dashboard.hr.interviews_schedule',
        createLabelFallback: 'Schedule Interview',
        moduleKey: 'hr',
        dashboardHref: '/dashboard/modules/hr',
        fields: [
          { name: 'application_id', label: 'Application ID', type: 'number', required: true },
          { name: 'type', label: 'Type', type: 'text', required: true },
          { name: 'scheduled_at', label: 'Scheduled At', type: 'text', required: true },
          { name: 'duration_minutes', label: 'Duration', type: 'number' },
          { name: 'location', label: 'Location', type: 'text' },
          { name: 'meeting_link', label: 'Meeting Link', type: 'url' },
        ],
        listFn: getInterviews,
        createFn: (payload) => scheduleInterview(Number(payload.application_id), payload),
        updateFn: null,
        deleteFn: null,
        columns: () => [
          { accessorKey: 'application_id', header: 'Application' },
          { accessorKey: 'candidate_id', header: 'Candidate' },
          { accessorKey: 'type', header: 'Type' },
          { accessorKey: 'scheduled_at', header: 'Scheduled At' },
          { accessorKey: 'status', header: 'Status' },
        ] as ColumnDef<RecruitmentInterview>[],
        toForm: () => ({
          application_id: '',
          type: 'video',
          scheduled_at: '',
          duration_minutes: '30',
          location: '',
          meeting_link: '',
        }),
        fromForm: (form) => ({
          application_id: parseInt(form.application_id),
          type: form.type,
          scheduled_at: form.scheduled_at,
          duration_minutes: form.duration_minutes ? parseInt(form.duration_minutes) : 30,
          location: form.location || undefined,
          meeting_link: form.meeting_link || undefined,
        }),
      }}
    />
  );
}
