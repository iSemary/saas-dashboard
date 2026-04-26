import { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Accounting Module',
  description: 'Manage chart of accounts, journal entries, budgets, and financial reporting',
};

export default function AccountingLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <div className="h-full">{children}</div>;
}
