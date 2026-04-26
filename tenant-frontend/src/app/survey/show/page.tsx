"use client";

import { Suspense } from "react";
import { useSearchParams } from "next/navigation";
import PublicSurveyPageClient from "@/app/s/public-survey-page-client";

function SurveyShowContent() {
  const searchParams = useSearchParams();
  const token = searchParams.get('token') ?? '';
  return <PublicSurveyPageClient token={token} />;
}

export default function SurveyShowPage() {
  return (
    <Suspense fallback={null}>
      <SurveyShowContent />
    </Suspense>
  );
}
