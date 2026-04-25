<?php

declare(strict_types=1);

namespace Modules\Survey\Domain\ValueObjects;

enum QuestionType: string
{
    case TEXT = 'text';
    case TEXTAREA = 'textarea';
    case EMAIL = 'email';
    case NUMBER = 'number';
    case PHONE = 'phone';
    case URL = 'url';
    case DATE = 'date';
    case MULTIPLE_CHOICE = 'multiple_choice';
    case CHECKBOX = 'checkbox';
    case DROPDOWN = 'dropdown';
    case RATING = 'rating';
    case NPS = 'nps';
    case LIKERT_SCALE = 'likert_scale';
    case MATRIX = 'matrix';
    case SLIDER = 'slider';
    case FILE_UPLOAD = 'file_upload';
    case IMAGE_CHOICE = 'image_choice';
    case RANKING = 'ranking';
    case YES_NO = 'yes_no';
    case SIGNATURE = 'signature';

    public static function fromString(string $value): self
    {
        return match($value) {
            'text' => self::TEXT,
            'textarea' => self::TEXTAREA,
            'email' => self::EMAIL,
            'number' => self::NUMBER,
            'phone' => self::PHONE,
            'url' => self::URL,
            'date' => self::DATE,
            'multiple_choice' => self::MULTIPLE_CHOICE,
            'checkbox' => self::CHECKBOX,
            'dropdown' => self::DROPDOWN,
            'rating' => self::RATING,
            'nps' => self::NPS,
            'likert_scale' => self::LIKERT_SCALE,
            'matrix' => self::MATRIX,
            'slider' => self::SLIDER,
            'file_upload' => self::FILE_UPLOAD,
            'image_choice' => self::IMAGE_CHOICE,
            'ranking' => self::RANKING,
            'yes_no' => self::YES_NO,
            'signature' => self::SIGNATURE,
            default => self::TEXT,
        };
    }

    public function label(): string
    {
        return match($this) {
            self::TEXT => 'Short Text',
            self::TEXTAREA => 'Long Text',
            self::EMAIL => 'Email',
            self::NUMBER => 'Number',
            self::PHONE => 'Phone Number',
            self::URL => 'Website',
            self::DATE => 'Date',
            self::MULTIPLE_CHOICE => 'Multiple Choice',
            self::CHECKBOX => 'Multiple Select (Checkboxes)',
            self::DROPDOWN => 'Dropdown',
            self::RATING => 'Star Rating',
            self::NPS => 'Net Promoter Score (NPS)',
            self::LIKERT_SCALE => 'Likert Scale',
            self::MATRIX => 'Matrix (Grid)',
            self::SLIDER => 'Slider',
            self::FILE_UPLOAD => 'File Upload',
            self::IMAGE_CHOICE => 'Picture Choice',
            self::RANKING => 'Ranking',
            self::YES_NO => 'Yes / No',
            self::SIGNATURE => 'Signature',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::TEXT => 'Type',
            self::TEXTAREA => 'AlignLeft',
            self::EMAIL => 'Mail',
            self::NUMBER => 'Hash',
            self::PHONE => 'Phone',
            self::URL => 'Globe',
            self::DATE => 'Calendar',
            self::MULTIPLE_CHOICE => 'CircleDot',
            self::CHECKBOX => 'SquareCheck',
            self::DROPDOWN => 'ChevronsUpDown',
            self::RATING => 'Star',
            self::NPS => 'TrendingUp',
            self::LIKERT_SCALE => 'Scale',
            self::MATRIX => 'Table',
            self::SLIDER => 'SlidersHorizontal',
            self::FILE_UPLOAD => 'Upload',
            self::IMAGE_CHOICE => 'Image',
            self::RANKING => 'ArrowUpDown',
            self::YES_NO => 'ToggleLeft',
            self::SIGNATURE => 'Pen',
        };
    }

    public function isChoiceType(): bool
    {
        return in_array($this, [
            self::MULTIPLE_CHOICE,
            self::CHECKBOX,
            self::DROPDOWN,
            self::IMAGE_CHOICE,
            self::RANKING,
        ], true);
    }

    public function isRatingType(): bool
    {
        return in_array($this, [
            self::RATING,
            self::NPS,
            self::LIKERT_SCALE,
            self::SLIDER,
        ], true);
    }

    public function isMatrixType(): bool
    {
        return $this === self::MATRIX;
    }

    public function requiresOptions(): bool
    {
        return $this->isChoiceType();
    }

    public function isNumericType(): bool
    {
        return in_array($this, [
            self::NUMBER,
            self::RATING,
            self::NPS,
            self::SLIDER,
        ], true);
    }

    public function isDateType(): bool
    {
        return $this === self::DATE;
    }

    public function isFileType(): bool
    {
        return $this === self::FILE_UPLOAD || $this === self::SIGNATURE;
    }

    public function supportsScoring(): bool
    {
        return $this->isChoiceType() || $this === self::YES_NO || $this->isRatingType();
    }

    public function getDefaultConfig(): array
    {
        return match($this) {
            self::TEXT => ['placeholder' => '', 'min_length' => null, 'max_length' => null],
            self::TEXTAREA => ['placeholder' => '', 'rows' => 4, 'min_length' => null, 'max_length' => null],
            self::EMAIL => ['placeholder' => 'email@example.com', 'verify_format' => true],
            self::NUMBER => ['min' => null, 'max' => null, 'step' => 1],
            self::PHONE => ['format' => 'international', 'placeholder' => '+1 (555) 000-0000'],
            self::URL => ['placeholder' => 'https://', 'verify_format' => true],
            self::DATE => ['min_date' => null, 'max_date' => null],
            self::MULTIPLE_CHOICE => ['allow_other' => false, 'shuffle_options' => false],
            self::CHECKBOX => ['allow_other' => false, 'min_select' => null, 'max_select' => null, 'shuffle_options' => false],
            self::DROPDOWN => ['allow_other' => false, 'placeholder' => 'Please select...'],
            self::RATING => ['max_rating' => 5, 'symbol' => 'star'],
            self::NPS => ['left_label' => 'Not at all likely', 'right_label' => 'Extremely likely'],
            self::LIKERT_SCALE => ['points' => 5, 'labels' => []],
            self::MATRIX => ['rows' => [], 'columns' => [], 'multiple_per_row' => false],
            self::SLIDER => ['min' => 0, 'max' => 100, 'step' => 1, 'default' => 50],
            self::FILE_UPLOAD => ['max_file_size' => 10485760, 'allowed_types' => ['pdf', 'jpg', 'png'], 'max_files' => 1],
            self::IMAGE_CHOICE => ['allow_multiple' => false, 'image_shape' => 'square'],
            self::RANKING => ['items' => [], 'allow_ties' => false],
            self::YES_NO => ['default_value' => null],
            self::SIGNATURE => ['pen_color' => '#000000', 'background_color' => '#ffffff', 'width' => 400, 'height' => 200],
        };
    }

    public function getAvailableOperators(): array
    {
        if ($this->isNumericType()) {
            return [
                BranchingOperator::EQUALS,
                BranchingOperator::NOT_EQUALS,
                BranchingOperator::GREATER_THAN,
                BranchingOperator::LESS_THAN,
                BranchingOperator::GREATER_OR_EQUAL,
                BranchingOperator::LESS_OR_EQUAL,
                BranchingOperator::IS_EMPTY,
                BranchingOperator::IS_NOT_EMPTY,
            ];
        }

        if ($this->isChoiceType()) {
            return [
                BranchingOperator::EQUALS,
                BranchingOperator::NOT_EQUALS,
                BranchingOperator::IN,
                BranchingOperator::NOT_IN,
                BranchingOperator::IS_EMPTY,
                BranchingOperator::IS_NOT_EMPTY,
            ];
        }

        if ($this === self::YES_NO) {
            return [
                BranchingOperator::EQUALS,
                BranchingOperator::NOT_EQUALS,
                BranchingOperator::IS_EMPTY,
                BranchingOperator::IS_NOT_EMPTY,
            ];
        }

        if ($this === self::TEXT || $this === self::TEXTAREA || $this === self::EMAIL || $this === self::URL) {
            return [
                BranchingOperator::EQUALS,
                BranchingOperator::NOT_EQUALS,
                BranchingOperator::CONTAINS,
                BranchingOperator::NOT_CONTAINS,
                BranchingOperator::IS_EMPTY,
                BranchingOperator::IS_NOT_EMPTY,
            ];
        }

        return [
            BranchingOperator::EQUALS,
            BranchingOperator::NOT_EQUALS,
            BranchingOperator::IS_EMPTY,
            BranchingOperator::IS_NOT_EMPTY,
        ];
    }
}
