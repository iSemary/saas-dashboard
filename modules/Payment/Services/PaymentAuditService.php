<?php

namespace Modules\Payment\Services;

use Modules\Payment\Entities\PaymentAuditLog;
use Illuminate\Support\Facades\Log;

class PaymentAuditService
{
    protected PaymentSecurityService $securityService;

    public function __construct(PaymentSecurityService $securityService)
    {
        $this->securityService = $securityService;
    }

    /**
     * Log payment operation for audit trail.
     *
     * @param string $operation
     * @param array $data
     * @param string|null $entityType
     * @param int|null $entityId
     * @param string|null $userId
     * @return void
     */
    public function logOperation(
        string $operation,
        array $data,
        ?string $entityType = null,
        ?int $entityId = null,
        ?string $userId = null
    ): void {
        try {
            $auditEntry = $this->securityService->generateAuditEntry($operation, $data, $userId);

            PaymentAuditLog::create([
                'operation' => $operation,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'user_id' => $userId ?? auth()->id(),
                'ip_address' => $auditEntry['ip_address'],
                'user_agent' => $auditEntry['user_agent'],
                'session_id' => $auditEntry['session_id'],
                'data' => $auditEntry['data'],
                'created_at' => now(),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to create audit log', [
                'operation' => $operation,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Log payment transaction events.
     *
     * @param string $transactionId
     * @param string $event
     * @param array $data
     * @return void
     */
    public function logTransactionEvent(string $transactionId, string $event, array $data): void
    {
        $this->logOperation(
            "transaction.{$event}",
            array_merge($data, ['transaction_id' => $transactionId]),
            'PaymentTransaction',
            null
        );
    }

    /**
     * Log payment method configuration changes.
     *
     * @param int $paymentMethodId
     * @param string $action
     * @param array $changes
     * @return void
     */
    public function logPaymentMethodChange(int $paymentMethodId, string $action, array $changes): void
    {
        $this->logOperation(
            "payment_method.{$action}",
            $changes,
            'PaymentMethod',
            $paymentMethodId
        );
    }

    /**
     * Log refund operations.
     *
     * @param string $refundId
     * @param string $action
     * @param array $data
     * @return void
     */
    public function logRefundOperation(string $refundId, string $action, array $data): void
    {
        $this->logOperation(
            "refund.{$action}",
            array_merge($data, ['refund_id' => $refundId]),
            'Refund',
            null
        );
    }

    /**
     * Log chargeback events.
     *
     * @param string $chargebackId
     * @param string $event
     * @param array $data
     * @return void
     */
    public function logChargebackEvent(string $chargebackId, string $event, array $data): void
    {
        $this->logOperation(
            "chargeback.{$event}",
            array_merge($data, ['chargeback_id' => $chargebackId]),
            'Chargeback',
            null
        );
    }

    /**
     * Log webhook events.
     *
     * @param string $gateway
     * @param string $event
     * @param array $data
     * @return void
     */
    public function logWebhookEvent(string $gateway, string $event, array $data): void
    {
        $this->logOperation(
            "webhook.{$gateway}.{$event}",
            $data,
            'Webhook',
            null
        );
    }

    /**
     * Log security events.
     *
     * @param string $event
     * @param array $data
     * @return void
     */
    public function logSecurityEvent(string $event, array $data): void
    {
        $this->logOperation(
            "security.{$event}",
            $data,
            'Security',
            null
        );

        // Also log to Laravel's security log
        Log::channel('security')->warning("Payment security event: {$event}", $data);
    }

    /**
     * Get audit logs for entity.
     *
     * @param string $entityType
     * @param int $entityId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getEntityAuditLogs(string $entityType, int $entityId, int $limit = 50)
    {
        return PaymentAuditLog::where('entity_type', $entityType)
                             ->where('entity_id', $entityId)
                             ->orderBy('created_at', 'desc')
                             ->limit($limit)
                             ->get();
    }

    /**
     * Get audit logs for user.
     *
     * @param int $userId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserAuditLogs(int $userId, int $limit = 100)
    {
        return PaymentAuditLog::where('user_id', $userId)
                             ->orderBy('created_at', 'desc')
                             ->limit($limit)
                             ->get();
    }

    /**
     * Search audit logs.
     *
     * @param array $criteria
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function searchAuditLogs(array $criteria)
    {
        $query = PaymentAuditLog::query();

        if (isset($criteria['operation'])) {
            $query->where('operation', 'like', "%{$criteria['operation']}%");
        }

        if (isset($criteria['entity_type'])) {
            $query->where('entity_type', $criteria['entity_type']);
        }

        if (isset($criteria['user_id'])) {
            $query->where('user_id', $criteria['user_id']);
        }

        if (isset($criteria['ip_address'])) {
            $query->where('ip_address', $criteria['ip_address']);
        }

        if (isset($criteria['date_from'])) {
            $query->where('created_at', '>=', $criteria['date_from']);
        }

        if (isset($criteria['date_to'])) {
            $query->where('created_at', '<=', $criteria['date_to']);
        }

        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Generate compliance audit report.
     *
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return array
     */
    public function generateComplianceReport(\DateTime $startDate, \DateTime $endDate): array
    {
        $logs = PaymentAuditLog::whereBetween('created_at', [$startDate, $endDate])->get();

        $report = [
            'period' => [
                'start' => $startDate->format('Y-m-d H:i:s'),
                'end' => $endDate->format('Y-m-d H:i:s'),
            ],
            'total_events' => $logs->count(),
            'operations' => $logs->groupBy('operation')->map->count(),
            'users' => $logs->whereNotNull('user_id')->groupBy('user_id')->map->count(),
            'entities' => $logs->whereNotNull('entity_type')->groupBy('entity_type')->map->count(),
            'security_events' => $logs->where('operation', 'like', 'security.%')->count(),
            'transaction_events' => $logs->where('operation', 'like', 'transaction.%')->count(),
            'payment_method_changes' => $logs->where('operation', 'like', 'payment_method.%')->count(),
            'generated_at' => now()->toISOString(),
        ];

        return $report;
    }

    /**
     * Clean up old audit logs based on retention policy.
     *
     * @param int $retentionDays
     * @return int
     */
    public function cleanupOldLogs(int $retentionDays = 2555): int // 7 years default
    {
        $cutoffDate = now()->subDays($retentionDays);
        
        return PaymentAuditLog::where('created_at', '<', $cutoffDate)->delete();
    }
}
