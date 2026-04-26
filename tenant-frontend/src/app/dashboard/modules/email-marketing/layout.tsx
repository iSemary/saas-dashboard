import { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Email Marketing Module',
  description: 'Manage email campaigns, templates, contact lists, automation, and analytics',
};

export default function EmailMarketingLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <div className="h-full">{children}</div>;
}
