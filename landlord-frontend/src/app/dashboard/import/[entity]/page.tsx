import { ImportPageClient } from "./ImportPageClient";

// Entity display names
const ENTITY_NAMES: Record<string, string> = {
  branches: "Branches",
  brands: "Brands",
  tenants: "Tenants",
  users: "Users",
  currencies: "Currencies",
  countries: "Countries",
  provinces: "Provinces",
  cities: "Cities",
  categories: "Categories",
  tags: "Tags",
  types: "Types",
  units: "Units",
  industries: "Industries",
  releases: "Releases",
  "static-pages": "Static Pages",
  announcements: "Announcements",
  languages: "Languages",
  "email-templates": "Email Templates",
};

export function generateStaticParams() {
  return Object.keys(ENTITY_NAMES).map((entity) => ({ entity }));
}

interface PageProps {
  params: Promise<{ entity: string }>;
}

export default async function GenericImportPage({ params }: PageProps) {
  const { entity } = await params;
  const entityName = ENTITY_NAMES[entity] || entity;

  return <ImportPageClient entity={entity} entityName={entityName} />;
}
