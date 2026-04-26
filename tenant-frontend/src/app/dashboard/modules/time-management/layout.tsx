import { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Time Management Module',
  description: 'Track time, manage calendars, and monitor productivity',
};

export default function TimeManagementLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <div className="h-full">{children}</div>;
}
