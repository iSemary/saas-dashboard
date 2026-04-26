import { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Project Management Module',
  description: 'Manage projects, tasks, boards, and delivery',
};

export default function ProjectManagementLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <div className="h-full">{children}</div>;
}
