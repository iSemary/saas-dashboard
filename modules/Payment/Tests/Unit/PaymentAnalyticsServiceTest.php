<?php

namespace Modules\Payment\Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Payment\Services\PaymentAnalyticsService;
use Modules\Payment\Entities\PaymentMethod;
use Modules\Payment\Entities\PaymentTransaction;
use Modules\Payment\Entities\Refund;
use Modules\Payment\Entities\Chargeback;
use Modules\Utilities\Entities\Currency;
use Carbon\Carbon;

class PaymentAnalyticsServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PaymentAnalyticsService $analyticsService;
    protected PaymentMethod $paymentMethod;
    protected Currency $currency;

    protected function setUp(): void
    {
        parent::setUp();

        $this->analyticsService = new PaymentAnalyticsService();

        // Create test currency
        $this->currency = Currency::create([
            'code' => 'USD',
            'name' => 'US Dollar',
            'symbol' => '$',
            'decimal_places' => 2,
            'exchange_rate' => 1.0,
            'status' => 'active',
        ]);

        // Create test payment method
        $this->paymentMethod = PaymentMethod::create([
            'name' => 'Test Gateway',
            'processor_type' => 'stripe',
            'gateway_name' => 'stripe_test',
            'supported_currencies' => ['USD'],
            'is_global' => true,
            'status' => 'active',
        ]);
    }

    public function test_get_payment_overview_with_successful_transactions()
    {
        // Create test transactions
        $this->createTestTransactions();

        $overview = $this->analyticsService->getPaymentOverview('30d');

        $this->assertIsArray($overview);
        $this->assertArrayHasKey('total_transactions', $overview);
        $this->assertArrayHasKey('successful_transactions', $overview);
        $this->assertArrayHasKey('success_rate', $overview);
        $this->assertArrayHasKey('total_volume', $overview);
        $this->assertArrayHasKey('net_volume', $overview);

        $this->assertEquals(3, $overview['total_transactions']);
        $this->assertEquals(2, $overview['successful_transactions']);
        $this->assertEquals(66.67, $overview['success_rate']);
        $this->assertEquals(300.00, $overview['total_volume']); // 100 + 200 from completed transactions
    }

    public function test_get_payment_method_performance()
    {
        $this->createTestTransactions();

        $performance = $this->analyticsService->getPaymentMethodPerformance('30d');

        $this->assertIsArray($performance);
        $this->assertNotEmpty($performance);

        $methodPerformance = $performance[0];
        $this->assertArrayHasKey('id', $methodPerformance);
        $this->assertArrayHasKey('name', $methodPerformance);
        $this->assertArrayHasKey('processor_type', $methodPerformance);
        $this->assertArrayHasKey('total_transactions', $methodPerformance);
        $this->assertArrayHasKey('successful_transactions', $methodPerformance);
        $this->assertArrayHasKey('success_rate', $methodPerformance);
        $this->assertArrayHasKey('total_volume', $methodPerformance);

        $this->assertEquals($this->paymentMethod->id, $methodPerformance['id']);
        $this->assertEquals('Test Gateway', $methodPerformance['name']);
        $this->assertEquals(3, $methodPerformance['total_transactions']);
        $this->assertEquals(2, $methodPerformance['successful_transactions']);
    }

    public function test_get_transaction_trends_by_day()
    {
        $this->createTestTransactionsOverTime();

        $trends = $this->analyticsService->getTransactionTrends('7d', 'day');

        $this->assertIsArray($trends);
        $this->assertNotEmpty($trends);

        foreach ($trends as $trend) {
            $this->assertArrayHasKey('period', $trend);
            $this->assertArrayHasKey('total_transactions', $trend);
            $this->assertArrayHasKey('successful_transactions', $trend);
            $this->assertArrayHasKey('total_volume', $trend);
            $this->assertArrayHasKey('success_rate', $trend);
        }
    }

    public function test_get_currency_distribution()
    {
        // Create transactions in different currencies
        $eurCurrency = Currency::create([
            'code' => 'EUR',
            'name' => 'Euro',
            'symbol' => '€',
            'decimal_places' => 2,
            'exchange_rate' => 0.85,
            'status' => 'active',
        ]);

        PaymentTransaction::create([
            'transaction_id' => 'txn_usd_1',
            'payment_method_id' => $this->paymentMethod->id,
            'currency_id' => $this->currency->id,
            'amount' => 100.00,
            'status' => 'completed',
            'transaction_type' => 'sale',
            'created_at' => now()->subDays(5),
        ]);

        PaymentTransaction::create([
            'transaction_id' => 'txn_eur_1',
            'payment_method_id' => $this->paymentMethod->id,
            'currency_id' => $eurCurrency->id,
            'amount' => 85.00,
            'status' => 'completed',
            'transaction_type' => 'sale',
            'created_at' => now()->subDays(3),
        ]);

        $distribution = $this->analyticsService->getCurrencyDistribution('30d');

        $this->assertIsArray($distribution);
        $this->assertCount(2, $distribution);

        // Check USD distribution
        $usdDistribution = collect($distribution)->firstWhere('code', 'USD');
        $this->assertNotNull($usdDistribution);
        $this->assertEquals('USD', $usdDistribution['code']);
        $this->assertEquals(1, $usdDistribution['transaction_count']);
        $this->assertEquals(100.00, $usdDistribution['total_volume']);

        // Check EUR distribution
        $eurDistribution = collect($distribution)->firstWhere('code', 'EUR');
        $this->assertNotNull($eurDistribution);
        $this->assertEquals('EUR', $eurDistribution['code']);
        $this->assertEquals(1, $eurDistribution['transaction_count']);
        $this->assertEquals(85.00, $eurDistribution['total_volume']);
    }

    public function test_get_failure_analysis()
    {
        // Create failed transactions with different error codes
        PaymentTransaction::create([
            'transaction_id' => 'txn_failed_1',
            'payment_method_id' => $this->paymentMethod->id,
            'currency_id' => $this->currency->id,
            'amount' => 100.00,
            'status' => 'failed',
            'transaction_type' => 'sale',
            'error_details' => json_encode(['code' => 'CARD_DECLINED', 'message' => 'Card was declined']),
            'created_at' => now()->subDays(2),
        ]);

        PaymentTransaction::create([
            'transaction_id' => 'txn_failed_2',
            'payment_method_id' => $this->paymentMethod->id,
            'currency_id' => $this->currency->id,
            'amount' => 50.00,
            'status' => 'failed',
            'transaction_type' => 'sale',
            'error_details' => json_encode(['code' => 'INSUFFICIENT_FUNDS', 'message' => 'Insufficient funds']),
            'created_at' => now()->subDays(1),
        ]);

        $analysis = $this->analyticsService->getFailureAnalysis('30d');

        $this->assertIsArray($analysis);
        $this->assertArrayHasKey('top_error_codes', $analysis);
        $this->assertArrayHasKey('failures_by_gateway', $analysis);

        $this->assertNotEmpty($analysis['top_error_codes']);
        $this->assertNotEmpty($analysis['failures_by_gateway']);
    }

    public function test_get_real_time_metrics()
    {
        // Create recent transactions
        PaymentTransaction::create([
            'transaction_id' => 'txn_recent_1',
            'payment_method_id' => $this->paymentMethod->id,
            'currency_id' => $this->currency->id,
            'amount' => 100.00,
            'status' => 'completed',
            'transaction_type' => 'sale',
            'created_at' => now()->subMinutes(30),
        ]);

        PaymentTransaction::create([
            'transaction_id' => 'txn_recent_2',
            'payment_method_id' => $this->paymentMethod->id,
            'currency_id' => $this->currency->id,
            'amount' => 50.00,
            'status' => 'pending',
            'transaction_type' => 'sale',
            'created_at' => now()->subMinutes(10),
        ]);

        $metrics = $this->analyticsService->getRealTimeMetrics();

        $this->assertIsArray($metrics);
        $this->assertArrayHasKey('transactions_last_hour', $metrics);
        $this->assertArrayHasKey('successful_last_hour', $metrics);
        $this->assertArrayHasKey('volume_last_hour', $metrics);
        $this->assertArrayHasKey('transactions_last_24h', $metrics);
        $this->assertArrayHasKey('successful_last_24h', $metrics);
        $this->assertArrayHasKey('volume_last_24h', $metrics);
        $this->assertArrayHasKey('active_payment_methods', $metrics);
        $this->assertArrayHasKey('pending_transactions', $metrics);

        $this->assertEquals(2, $metrics['transactions_last_hour']);
        $this->assertEquals(1, $metrics['successful_last_hour']);
        $this->assertEquals(100.00, $metrics['volume_last_hour']);
        $this->assertEquals(1, $metrics['pending_transactions']);
    }

    public function test_get_reconciliation_report()
    {
        $date = now()->format('Y-m-d');

        // Create transactions for today
        PaymentTransaction::create([
            'transaction_id' => 'txn_today_1',
            'payment_method_id' => $this->paymentMethod->id,
            'currency_id' => $this->currency->id,
            'amount' => 100.00,
            'status' => 'completed',
            'transaction_type' => 'sale',
            'created_at' => now(),
        ]);

        // Create refund for today
        $refund = Refund::create([
            'refund_id' => 'ref_today_1',
            'original_transaction_id' => 1, // Assuming first transaction ID
            'amount' => 25.00,
            'reason' => 'requested_by_customer',
            'status' => 'processed',
            'created_at' => now(),
        ]);

        $report = $this->analyticsService->getReconciliationReport($date);

        $this->assertIsArray($report);
        $this->assertArrayHasKey('summary', $report);
        $this->assertArrayHasKey('by_gateway', $report);
        $this->assertArrayHasKey('transactions', $report);
        $this->assertArrayHasKey('refunds', $report);

        $summary = $report['summary'];
        $this->assertEquals($date, $summary['date']);
        $this->assertEquals(1, $summary['total_transactions']);
        $this->assertEquals(100.00, $summary['total_volume']);
        $this->assertEquals(25.00, $summary['total_refunds']);
        $this->assertEquals(75.00, $summary['net_volume']);
    }

    public function test_check_alert_conditions()
    {
        // Create high failure rate scenario
        for ($i = 0; $i < 15; $i++) {
            PaymentTransaction::create([
                'transaction_id' => "txn_fail_{$i}",
                'payment_method_id' => $this->paymentMethod->id,
                'currency_id' => $this->currency->id,
                'amount' => 100.00,
                'status' => 'failed',
                'transaction_type' => 'sale',
                'created_at' => now()->subMinutes(5),
            ]);
        }

        // Create some successful transactions
        for ($i = 0; $i < 5; $i++) {
            PaymentTransaction::create([
                'transaction_id' => "txn_success_{$i}",
                'payment_method_id' => $this->paymentMethod->id,
                'currency_id' => $this->currency->id,
                'amount' => 100.00,
                'status' => 'completed',
                'transaction_type' => 'sale',
                'created_at' => now()->subMinutes(5),
            ]);
        }

        $alerts = $this->analyticsService->checkAlertConditions();

        $this->assertIsArray($alerts);
        $this->assertNotEmpty($alerts);

        // Should have high failure rate alert
        $failureAlert = collect($alerts)->firstWhere('type', 'high_failure_rate');
        $this->assertNotNull($failureAlert);
        $this->assertEquals('critical', $failureAlert['severity']);
    }

    protected function createTestTransactions()
    {
        // Successful transaction 1
        PaymentTransaction::create([
            'transaction_id' => 'txn_success_1',
            'payment_method_id' => $this->paymentMethod->id,
            'currency_id' => $this->currency->id,
            'amount' => 100.00,
            'status' => 'completed',
            'transaction_type' => 'sale',
            'processing_time_ms' => 1500,
            'created_at' => now()->subDays(5),
        ]);

        // Successful transaction 2
        PaymentTransaction::create([
            'transaction_id' => 'txn_success_2',
            'payment_method_id' => $this->paymentMethod->id,
            'currency_id' => $this->currency->id,
            'amount' => 200.00,
            'status' => 'completed',
            'transaction_type' => 'sale',
            'processing_time_ms' => 2000,
            'created_at' => now()->subDays(3),
        ]);

        // Failed transaction
        PaymentTransaction::create([
            'transaction_id' => 'txn_failed_1',
            'payment_method_id' => $this->paymentMethod->id,
            'currency_id' => $this->currency->id,
            'amount' => 150.00,
            'status' => 'failed',
            'transaction_type' => 'sale',
            'error_details' => json_encode(['code' => 'CARD_DECLINED']),
            'created_at' => now()->subDays(2),
        ]);
    }

    protected function createTestTransactionsOverTime()
    {
        $dates = [
            now()->subDays(6),
            now()->subDays(5),
            now()->subDays(4),
            now()->subDays(3),
            now()->subDays(2),
            now()->subDays(1),
            now(),
        ];

        foreach ($dates as $index => $date) {
            PaymentTransaction::create([
                'transaction_id' => "txn_trend_{$index}",
                'payment_method_id' => $this->paymentMethod->id,
                'currency_id' => $this->currency->id,
                'amount' => 100.00 + ($index * 10),
                'status' => $index % 2 === 0 ? 'completed' : 'failed',
                'transaction_type' => 'sale',
                'created_at' => $date,
            ]);
        }
    }
}
