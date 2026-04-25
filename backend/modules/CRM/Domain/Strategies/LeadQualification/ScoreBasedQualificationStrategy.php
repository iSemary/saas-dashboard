<?php

declare(strict_types=1);

namespace Modules\CRM\Domain\Strategies\LeadQualification;

use Modules\CRM\Domain\Entities\Lead;
use Modules\CRM\Domain\ValueObjects\LeadStatus;

/**
 * Score-based qualification strategy that assigns points for various lead attributes.
 */
class ScoreBasedQualificationStrategy implements LeadQualificationStrategyInterface
{
    private const QUALIFIED_THRESHOLD = 60;

    public function canQualify(Lead $lead): bool
    {
        $disqualifiedStatuses = [
            LeadStatus::QUALIFIED->value,
            LeadStatus::CONVERTED->value,
        ];

        return !in_array($lead->status, $disqualifiedStatuses, true);
    }

    public function qualify(Lead $lead): LeadQualificationResult
    {
        if (!$this->canQualify($lead)) {
            return new LeadQualificationResult(
                isQualified: false,
                reason: 'Lead cannot be qualified in current status: ' . $lead->status
            );
        }

        $scoring = $this->calculateScore($lead);
        $isQualified = $scoring['total'] >= self::QUALIFIED_THRESHOLD;

        return new LeadQualificationResult(
            isQualified: $isQualified,
            score: $scoring['total'],
            reason: $isQualified
                ? "Lead qualified with score {$scoring['total']}/100 (threshold: " . self::QUALIFIED_THRESHOLD . ')'
                : "Lead not qualified (score {$scoring['total']}, threshold: " . self::QUALIFIED_THRESHOLD . ')',
            criteria: $scoring['breakdown']
        );
    }

    public function getName(): string
    {
        return 'Score-Based Qualification';
    }

    public function getDescription(): string
    {
        return 'Qualifies leads based on a weighted scoring system across multiple dimensions.';
    }

    private function calculateScore(Lead $lead): array
    {
        $breakdown = [];
        $totalScore = 0;

        // Contact Information (max 30 points)
        $contactScore = 0;
        if (!empty($lead->email) && filter_var($lead->email, FILTER_VALIDATE_EMAIL)) {
            $contactScore += 15;
        }
        if (!empty($lead->phone)) {
            $contactScore += 10;
        }
        if (!empty($lead->name) && strlen($lead->name) > 2) {
            $contactScore += 5;
        }
        $breakdown[] = ['category' => 'Contact Information', 'score' => $contactScore, 'max' => 30];
        $totalScore += $contactScore;

        // Company Information (max 20 points)
        $companyScore = 0;
        if (!empty($lead->company)) {
            $companyScore += 15;
        }
        if (!empty($lead->title)) {
            $companyScore += 5;
        }
        $breakdown[] = ['category' => 'Company Information', 'score' => $companyScore, 'max' => 20];
        $totalScore += $companyScore;

        // Deal Potential (max 30 points)
        $dealScore = 0;
        if (!empty($lead->expected_revenue)) {
            if ($lead->expected_revenue >= 10000) {
                $dealScore += 30;
            } elseif ($lead->expected_revenue >= 5000) {
                $dealScore += 20;
            } elseif ($lead->expected_revenue >= 1000) {
                $dealScore += 10;
            }
        }
        if (!empty($lead->expected_close_date)) {
            $daysUntilClose = now()->diffInDays($lead->expected_close_date, false);
            if ($daysUntilClose > 0 && $daysUntilClose <= 30) {
                $dealScore += 10; // Urgent deal
            } elseif ($daysUntilClose > 0 && $daysUntilClose <= 90) {
                $dealScore += 5;
            }
        }
        $breakdown[] = ['category' => 'Deal Potential', 'score' => min($dealScore, 30), 'max' => 30];
        $totalScore += min($dealScore, 30);

        // Source Quality (max 20 points)
        $sourceScore = match ($lead->source) {
            'referral' => 20,
            'partner' => 18,
            'trade_show' => 15,
            'advertisement' => 12,
            'website' => 10,
            'email' => 8,
            'phone' => 8,
            'social' => 5,
            default => 3,
        };
        $breakdown[] = ['category' => 'Source Quality', 'score' => $sourceScore, 'max' => 20];
        $totalScore += $sourceScore;

        return [
            'total' => min($totalScore, 100),
            'breakdown' => $breakdown,
        ];
    }
}
