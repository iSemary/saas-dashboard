'use client';

import { useEffect, useState, useCallback } from 'react';
import { useParams } from 'next/navigation';
import { getPublicSurvey, startSurveyResponse, submitAnswer, completeSurveyResponse } from '@/lib/api-survey';
import { PublicSurveyRenderer } from '@/components/survey/public-survey-renderer';
import { Survey, SurveyShare, SurveyResponse } from '@/lib/api-survey';
import { Card, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Loader2 } from 'lucide-react';

export default function PublicSurveyPage() {
  const params = useParams();
  const token = params.token as string;

  const [survey, setSurvey] = useState<Survey | null>(null);
  const [share, setShare] = useState<SurveyShare | null>(null);
  const [response, setResponse] = useState<SurveyResponse | null>(null);
  const [resumeToken, setResumeToken] = useState<string | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [currentPage, setCurrentPage] = useState(0);
  const [answers, setAnswers] = useState<Record<number, any>>({});
  const [completed, setCompleted] = useState(false);

  useEffect(() => {
    loadSurvey();
  }, [token]);

  const loadSurvey = async () => {
    try {
      const data = await getPublicSurvey(token);
      setSurvey(data.survey);
      setShare(data.share);
    } catch (err) {
      setError('Survey not found or expired');
    } finally {
      setLoading(false);
    }
  };

  const handleStart = async (respondentData: { email?: string; name?: string }) => {
    try {
      const result = await startSurveyResponse(token, respondentData);
      setResponse(result.response);
      setResumeToken(result.resume_token);
    } catch (err) {
      setError('Failed to start survey');
    }
  };

  const handleAnswer = useCallback(async (questionId: number, value: any) => {
    setAnswers(prev => ({ ...prev, [questionId]: value }));

    if (response) {
      try {
        await submitAnswer(token, {
          response_id: response.id,
          question_id: questionId,
          value,
        });
      } catch (err) {
        console.error('Failed to save answer:', err);
      }
    }
  }, [response, token]);

  const handleComplete = async () => {
    if (!response) return;

    try {
      await completeSurveyResponse(token, { response_id: response.id });
      setCompleted(true);
    } catch (err) {
      setError('Failed to complete survey');
    }
  };

  const handleNextPage = () => {
    if (survey?.pages && currentPage < survey.pages.length - 1) {
      setCurrentPage(prev => prev + 1);
    }
  };

  const handlePrevPage = () => {
    if (currentPage > 0) {
      setCurrentPage(prev => prev - 1);
    }
  };

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <Loader2 className="w-8 h-8 animate-spin" />
      </div>
    );
  }

  if (error) {
    return (
      <div className="min-h-screen flex items-center justify-center p-4">
        <Card className="max-w-md w-full">
          <CardContent className="p-6 text-center">
            <p className="text-red-500">{error}</p>
          </CardContent>
        </Card>
      </div>
    );
  }

  if (completed) {
    return (
      <div className="min-h-screen flex items-center justify-center p-4">
        <Card className="max-w-md w-full">
          <CardContent className="p-6 text-center">
            <h1 className="text-2xl font-bold mb-4">Thank You!</h1>
            <p className="text-muted-foreground">
              {survey?.settings?.thank_you_message || 'Your response has been recorded.'}
            </p>
            {response?.score !== null && response && (
              <div className="mt-4 p-4 bg-primary/10 rounded-lg">
                <p className="text-lg font-semibold">
                  Your Score: {response.score} / {response.max_score}
                </p>
                {response.passed !== null && (
                  <p className={response.passed ? 'text-green-600' : 'text-red-600'}>
                    {response.passed ? 'Congratulations! You passed!' : 'You did not pass this time.'}
                  </p>
                )}
              </div>
            )}
          </CardContent>
        </Card>
      </div>
    );
  }

  if (!response) {
    return (
      <div className="min-h-screen flex items-center justify-center p-4">
        <Card className="max-w-md w-full">
          <CardContent className="p-6">
            <h1 className="text-2xl font-bold mb-2">{survey?.title}</h1>
            <p className="text-muted-foreground mb-6">{survey?.description}</p>
            <Button onClick={() => handleStart({})} className="w-full">
              Start Survey
            </Button>
          </CardContent>
        </Card>
      </div>
    );
  }

  return (
    <PublicSurveyRenderer
      survey={survey!}
      currentPage={currentPage}
      answers={answers}
      onAnswer={handleAnswer}
      onNext={handleNextPage}
      onPrev={handlePrevPage}
      onComplete={handleComplete}
      isFirstPage={currentPage === 0}
      isLastPage={!survey?.pages || currentPage === (survey.pages.length - 1)}
    />
  );
}
