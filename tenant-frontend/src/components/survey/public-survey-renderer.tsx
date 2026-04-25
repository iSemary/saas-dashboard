'use client';

import { motion, AnimatePresence } from 'framer-motion';
import { Survey, SurveyPage } from '@/lib/api-survey';
import { QuestionRenderer } from './question-renderer';
import { Button } from '@/components/ui/button';
import { Progress } from '@/components/ui/progress';
import { ChevronLeft, ChevronRight, Check } from 'lucide-react';

interface PublicSurveyRendererProps {
  survey: Survey;
  currentPage: number;
  answers: Record<number, any>;
  onAnswer: (questionId: number, value: any) => void;
  onNext: () => void;
  onPrev: () => void;
  onComplete: () => void;
  isFirstPage: boolean;
  isLastPage: boolean;
}

export function PublicSurveyRenderer({
  survey,
  currentPage,
  answers,
  onAnswer,
  onNext,
  onPrev,
  onComplete,
  isFirstPage,
  isLastPage,
}: PublicSurveyRendererProps) {
  const pages = survey.pages || [];
  const page = pages[currentPage];
  const progress = pages.length > 0 ? ((currentPage + 1) / pages.length) * 100 : 0;

  const canProceed = () => {
    if (!page?.questions) return true;
    return page.questions.every(q => {
      if (!q.is_required) return true;
      const answer = answers[q.id];
      return answer !== undefined && answer !== null && answer !== '';
    });
  };

  return (
    <div className="min-h-screen bg-background p-4 md:p-8">
      <div className="max-w-2xl mx-auto">
        {/* Header */}
        <div className="mb-8">
          <h1 className="text-2xl font-bold mb-2">{survey.title}</h1>
          {survey.settings?.show_progress_bar && (
            <div className="space-y-2">
              <Progress value={progress} className="h-2" />
              <p className="text-sm text-muted-foreground text-right">
                Page {currentPage + 1} of {pages.length}
              </p>
            </div>
          )}
        </div>

        {/* Questions */}
        <AnimatePresence mode="wait">
          <motion.div
            key={currentPage}
            initial={{ opacity: 0, x: 20 }}
            animate={{ opacity: 1, x: 0 }}
            exit={{ opacity: 0, x: -20 }}
            transition={{ duration: 0.3 }}
            className="space-y-6"
          >
            {page?.title && (
              <h2 className="text-xl font-semibold">{page.title}</h2>
            )}
            {page?.description && (
              <p className="text-muted-foreground">{page.description}</p>
            )}

            {page?.questions?.map((question, index) => (
              <QuestionRenderer
                key={question.id}
                question={question}
                value={answers[question.id]}
                onChange={(value) => onAnswer(question.id, value)}
                index={index + 1}
                showNumbers={survey.settings?.show_question_numbers !== false}
              />
            ))}
          </motion.div>
        </AnimatePresence>

        {/* Navigation */}
        <div className="flex justify-between mt-8 pt-4 border-t">
          <Button
            variant="outline"
            onClick={onPrev}
            disabled={isFirstPage}
            className="gap-2"
          >
            <ChevronLeft className="w-4 h-4" />
            Previous
          </Button>

          {isLastPage ? (
            <Button
              onClick={onComplete}
              disabled={!canProceed()}
              className="gap-2"
            >
              <Check className="w-4 h-4" />
              Complete
            </Button>
          ) : (
            <Button
              onClick={onNext}
              disabled={!canProceed()}
              className="gap-2"
            >
              Next
              <ChevronRight className="w-4 h-4" />
            </Button>
          )}
        </div>
      </div>
    </div>
  );
}
