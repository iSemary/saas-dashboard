import { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'SMS Marketing Module',
  description: 'Manage SMS campaigns, templates, contact lists, automation, and analytics',
};

export default function SmsMarketingLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <div className="h-full">{children}</div>;
}
