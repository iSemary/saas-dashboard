<?php

declare(strict_types=1);

namespace Modules\CRM\Domain\ValueObjects;

enum OpportunityStage: string
{
    case PROSPECTING = 'prospecting';
    case QUALIFICATION = 'qualification';
    case NEEDS_ANALYSIS = 'needs_analysis';
    case VALUE_PROPOSITION = 'value_proposition';
    case IDDECISION_MAKERS = 'id_decision_makers';
    case PERCEPTION_ANALYSIS = 'perception_analysis';
    case PROPOSAL_PRICE_QUOTE = 'proposal_price_quote';
    case NEGOTIATION_REVIEW = 'negotiation_review';
    case CLOSED_WON = 'closed_won';
    case CLOSED_LOST = 'closed_lost';

    public function label(): string
    {
        return match ($this) {
            self::PROSPECTING => 'Prospecting',
            self::QUALIFICATION => 'Qualification',
            self::NEEDS_ANALYSIS => 'Needs Analysis',
            self::VALUE_PROPOSITION => 'Value Proposition',
            self::IDDECISION_MAKERS => 'Identify Decision Makers',
            self::PERCEPTION_ANALYSIS => 'Perception Analysis',
            self::PROPOSAL_PRICE_QUOTE => 'Proposal/Price Quote',
            self::NEGOTIATION_REVIEW => 'Negotiation/Review',
            self::CLOSED_WON => 'Closed Won',
            self::CLOSED_LOST => 'Closed Lost',
        };
    }

    public function probability(): int
    {
        return match ($this) {
            self::PROSPECTING => 10,
            self::QUALIFICATION => 20,
            self::NEEDS_ANALYSIS => 30,
            self::VALUE_PROPOSITION => 40,
            self::IDDECISION_MAKERS => 50,
            self::PERCEPTION_ANALYSIS => 60,
            self::PROPOSAL_PRICE_QUOTE => 70,
            self::NEGOTIATION_REVIEW => 80,
            self::CLOSED_WON => 100,
            self::CLOSED_LOST => 0,
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PROSPECTING => 'gray',
            self::QUALIFICATION => 'blue',
            self::NEEDS_ANALYSIS => 'cyan',
            self::VALUE_PROPOSITION => 'indigo',
            self::IDDECISION_MAKERS => 'violet',
            self::PERCEPTION_ANALYSIS => 'purple',
            self::PROPOSAL_PRICE_QUOTE => 'fuchsia',
            self::NEGOTIATION_REVIEW => 'pink',
            self::CLOSED_WON => 'green',
            self::CLOSED_LOST => 'red',
        };
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::CLOSED_WON, self::CLOSED_LOST], true);
    }

    public function isOpen(): bool
    {
        return !$this->isTerminal();
    }

    public static function canTransitionFrom(self $from, self $to): bool
    {
        // Terminal stages cannot transition out
        if ($from->isTerminal()) {
            return false;
        }

        // Can transition to any open stage or terminal stage
        return true;
    }

    public static function openStages(): array
    {
        return array_filter(self::cases(), fn ($stage) => $stage->isOpen());
    }

    public static function fromString(string $value): self
    {
        return self::tryFrom($value) ?? self::PROSPECTING;
    }

    public static function all(): array
    {
        return array_map(fn ($case) => [
            'value' => $case->value,
            'label' => $case->label(),
            'probability' => $case->probability(),
            'color' => $case->color(),
            'is_terminal' => $case->isTerminal(),
        ], self::cases());
    }
}
