'use client';

import { SurveyQuestion } from '@/lib/api-survey';
import { TextQuestion } from './question-types/text-question';
import { TextareaQuestion } from './question-types/textarea-question';
import { MultipleChoiceQuestion } from './question-types/multiple-choice-question';
import { CheckboxQuestion } from './question-types/checkbox-question';
import { RatingQuestion } from './question-types/rating-question';
import { NpsQuestion } from './question-types/nps-question';
import { YesNoQuestion } from './question-types/yes-no-question';
import { DropdownQuestion } from './question-types/dropdown-question';
import { DateQuestion } from './question-types/date-question';
import { NumberQuestion } from './question-types/number-question';
import { EmailQuestion } from './question-types/email-question';
import { FileUploadQuestion } from './question-types/file-upload-question';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { Label } from '@/components/ui/label';

interface QuestionRendererProps {
  question: SurveyQuestion;
  value: any;
  onChange: (value: any) => void;
  index: number;
  showNumbers: boolean;
}

export function QuestionRenderer({
  question,
  value,
  onChange,
  index,
  showNumbers,
}: QuestionRendererProps) {
  const renderQuestion = () => {
    switch (question.type) {
      case 'text':
        return <TextQuestion value={value} onChange={onChange} config={question.config} />;
      case 'textarea':
        return <TextareaQuestion value={value} onChange={onChange} config={question.config} />;
      case 'multiple_choice':
        return <MultipleChoiceQuestion value={value} onChange={onChange} options={question.options || []} />;
      case 'checkbox':
        return <CheckboxQuestion value={value} onChange={onChange} options={question.options || []} />;
      case 'dropdown':
        return <DropdownQuestion value={value} onChange={onChange} options={question.options || []} />;
      case 'rating':
        return <RatingQuestion value={value} onChange={onChange} config={question.config} />;
      case 'nps':
        return <NpsQuestion value={value} onChange={onChange} />;
      case 'yes_no':
        return <YesNoQuestion value={value} onChange={onChange} />;
      case 'date':
        return <DateQuestion value={value} onChange={onChange} />;
      case 'number':
        return <NumberQuestion value={value} onChange={onChange} config={question.config} />;
      case 'email':
        return <EmailQuestion value={value} onChange={onChange} />;
      case 'file_upload':
        return <FileUploadQuestion value={value} onChange={onChange} config={question.config} />;
      default:
        return <TextQuestion value={value} onChange={onChange} config={question.config} />;
    }
  };

  return (
    <Card>
      <CardHeader className="pb-3">
        <div className="flex items-start gap-2">
          {showNumbers && (
            <span className="flex-shrink-0 w-6 h-6 rounded-full bg-primary/10 text-primary text-sm font-medium flex items-center justify-center">
              {index}
            </span>
          )}
          <div className="flex-1">
            <Label className="text-base font-medium leading-normal">
              {question.title}
              {question.is_required && <span className="text-red-500 ml-1">*</span>}
            </Label>
            {question.description && (
              <p className="text-sm text-muted-foreground mt-1">{question.description}</p>
            )}
            {question.help_text && (
              <p className="text-xs text-muted-foreground mt-1">{question.help_text}</p>
            )}
          </div>
        </div>
      </CardHeader>
      <CardContent>{renderQuestion()}</CardContent>
    </Card>
  );
}
