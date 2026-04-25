import { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'CRM Module',
  description: 'Customer Relationship Management',
};

export default function CRMLayout({ children }: { children: React.ReactNode }) {
  return <div className="h-full">{children}</div>;
}
