import { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Expenses Module',
  description: 'Manage expenses, approvals, reimbursements, and expense policies',
};

export default function ExpensesLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <div className="h-full">{children}</div>;
}
