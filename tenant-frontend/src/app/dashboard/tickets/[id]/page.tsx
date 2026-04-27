import TicketDetailClient from "./ticket-detail-client";

export const revalidate = 0;

export function generateStaticParams() {
  return [];
}

export default function TicketDetailPage() {
  return <TicketDetailClient />;
}
