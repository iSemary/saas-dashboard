import { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Survey Module',
  description: 'Create surveys and collect responses',
};

export default function SurveyLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return children;
}
