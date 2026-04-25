'use client';

import { SimpleCRUDPage } from '@/components/simple-crud-page';
import { RecruitmentOffer, getOffers, makeOffer } from '@/lib/api-hr';
import { ColumnDef } from '@tanstack/react-table';

export default function HrOffersPage() {
  return (
    <SimpleCRUDPage<RecruitmentOffer>
      config={{
        titleKey: 'dashboard.hr.offers_title',
        titleFallback: 'Offers',
        subtitleKey: 'dashboard.hr.offers_subtitle',
        subtitleFallback: 'Create and track job offers',
        createLabelKey: 'dashboard.hr.offers_create',
        createLabelFallback: 'Create Offer',
        moduleKey: 'hr',
        dashboardHref: '/dashboard/modules/hr',
        fields: [
          { name: 'application_id', label: 'Application ID', type: 'number', required: true },
          { name: 'salary', label: 'Salary', type: 'number', required: true },
          { name: 'currency', label: 'Currency', type: 'text', required: true },
          { name: 'bonus', label: 'Bonus', type: 'number' },
          { name: 'start_date', label: 'Start Date', type: 'text', required: true },
          { name: 'expiry_date', label: 'Expiry Date', type: 'text', required: true },
        ],
        listFn: getOffers,
        createFn: (payload) => makeOffer(Number(payload.application_id), payload),
        updateFn: null,
        deleteFn: null,
        columns: () => [
          { accessorKey: 'application_id', header: 'Application' },
          { accessorKey: 'candidate_id', header: 'Candidate' },
          { accessorKey: 'salary', header: 'Salary' },
          { accessorKey: 'currency', header: 'Currency' },
          { accessorKey: 'status', header: 'Status' },
        ] as ColumnDef<RecruitmentOffer>[],
        toForm: () => ({
          application_id: '',
          salary: '',
          currency: 'USD',
          bonus: '0',
          start_date: '',
          expiry_date: '',
        }),
        fromForm: (form) => ({
          application_id: parseInt(form.application_id),
          salary: parseFloat(form.salary),
          currency: form.currency || 'USD',
          bonus: form.bonus ? parseFloat(form.bonus) : 0,
          start_date: form.start_date,
          expiry_date: form.expiry_date,
        }),
      }}
    />
  );
}
