import { Suspense } from "react";
import PublicSurveyPageClient from "../s/public-survey-page-client";

export default function PublicSurveyPage() {
  return (
    <Suspense fallback={null}>
      <PublicSurveyPageClient />
    </Suspense>
  );
}
