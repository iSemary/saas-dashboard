<?php

namespace Modules\Payment\Services;

use Modules\Utilities\Entities\Currency;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CurrencyConversionService
{
    protected string $baseCurrency;
    protected array $providers = [
        'exchangerate_api' => 'https://api.exchangerate-api.com/v4/latest/',
        'fixer' => 'https://api.fixer.io/latest',
        'currencylayer' => 'https://api.currencylayer.com/live',
    ];

    public function __construct()
    {
        $this->baseCurrency = $this->getBaseCurrency();
    }

    /**
     * Convert amount from one currency to another.
     *
     * @param float $amount
     * @param string $fromCurrency
     * @param string $toCurrency
     * @return array
     */
    public function convert(float $amount, string $fromCurrency, string $toCurrency): array
    {
        if ($fromCurrency === $toCurrency) {
            return [
                'original_amount' => $amount,
                'converted_amount' => $amount,
                'from_currency' => $fromCurrency,
                'to_currency' => $toCurrency,
                'exchange_rate' => 1.0,
                'conversion_date' => now(),
            ];
        }

        $exchangeRate = $this->getExchangeRate($fromCurrency, $toCurrency);
        $convertedAmount = $amount * $exchangeRate;

        return [
            'original_amount' => $amount,
            'converted_amount' => round($convertedAmount, 2),
            'from_currency' => $fromCurrency,
            'to_currency' => $toCurrency,
            'exchange_rate' => $exchangeRate,
            'conversion_date' => now(),
        ];
    }

    /**
     * Get exchange rate between two currencies.
     *
     * @param string $fromCurrency
     * @param string $toCurrency
     * @return float
     */
    public function getExchangeRate(string $fromCurrency, string $toCurrency): float
    {
        if ($fromCurrency === $toCurrency) {
            return 1.0;
        }

        $cacheKey = "exchange_rate_{$fromCurrency}_{$toCurrency}";
        
        return Cache::remember($cacheKey, 3600, function () use ($fromCurrency, $toCurrency) {
            // First try to get rate from database
            $rate = $this->getExchangeRateFromDatabase($fromCurrency, $toCurrency);
            
            if ($rate !== null) {
                return $rate;
            }

            // Fallback to external API
            return $this->getExchangeRateFromAPI($fromCurrency, $toCurrency);
        });
    }

    /**
     * Update exchange rates from external API.
     *
     * @param array $currencies
     * @return bool
     */
    public function updateExchangeRates(array $currencies = []): bool
    {
        try {
            if (empty($currencies)) {
                $currencies = Currency::active()->pluck('code')->toArray();
            }

            $rates = $this->fetchLatestRates($this->baseCurrency);
            
            if (empty($rates)) {
                return false;
            }

            foreach ($currencies as $currencyCode) {
                if ($currencyCode === $this->baseCurrency) {
                    continue;
                }

                $rate = $rates[$currencyCode] ?? null;
                
                if ($rate) {
                    Currency::where('code', $currencyCode)->update([
                        'exchange_rate' => $rate,
                        'exchange_rate_last_updated' => now(),
                    ]);
                }
            }

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to update exchange rates', [
                'error' => $e->getMessage(),
                'currencies' => $currencies,
            ]);
            return false;
        }
    }

    /**
     * Get supported currencies.
     *
     * @return array
     */
    public function getSupportedCurrencies(): array
    {
        return Currency::active()->pluck('name', 'code')->toArray();
    }

    /**
     * Format amount with currency.
     *
     * @param float $amount
     * @param string $currencyCode
     * @return string
     */
    public function formatAmount(float $amount, string $currencyCode): string
    {
        $currency = Currency::where('code', $currencyCode)->first();
        
        if (!$currency) {
            return number_format($amount, 2);
        }

        $formatted = number_format($amount, $currency->decimal_places);
        
        if ($currency->symbol_position === 'left') {
            return $currency->symbol . ' ' . $formatted;
        } else {
            return $formatted . ' ' . $currency->symbol;
        }
    }

    /**
     * Get historical exchange rate.
     *
     * @param string $fromCurrency
     * @param string $toCurrency
     * @param string $date
     * @return float|null
     */
    public function getHistoricalRate(string $fromCurrency, string $toCurrency, string $date): ?float
    {
        $cacheKey = "historical_rate_{$fromCurrency}_{$toCurrency}_{$date}";
        
        return Cache::remember($cacheKey, 86400, function () use ($fromCurrency, $toCurrency, $date) {
            // Try to fetch from external API
            try {
                $response = Http::get("https://api.exchangerate-api.com/v4/history/{$fromCurrency}/{$date}");
                
                if ($response->successful()) {
                    $data = $response->json();
                    return $data['rates'][$toCurrency] ?? null;
                }
            } catch (\Exception $e) {
                Log::warning('Failed to fetch historical exchange rate', [
                    'from' => $fromCurrency,
                    'to' => $toCurrency,
                    'date' => $date,
                    'error' => $e->getMessage(),
                ]);
            }

            return null;
        });
    }

    /**
     * Calculate conversion fees.
     *
     * @param float $amount
     * @param string $fromCurrency
     * @param string $toCurrency
     * @return array
     */
    public function calculateConversionFees(float $amount, string $fromCurrency, string $toCurrency): array
    {
        if ($fromCurrency === $toCurrency) {
            return [
                'fee_amount' => 0,
                'fee_percentage' => 0,
                'net_amount' => $amount,
            ];
        }

        // Default conversion fee (can be configured)
        $feePercentage = config('payment.currency_conversion_fee', 0.5); // 0.5%
        $feeAmount = ($amount * $feePercentage) / 100;
        
        return [
            'fee_amount' => round($feeAmount, 2),
            'fee_percentage' => $feePercentage,
            'net_amount' => round($amount - $feeAmount, 2),
        ];
    }

    /**
     * Get exchange rate from database.
     *
     * @param string $fromCurrency
     * @param string $toCurrency
     * @return float|null
     */
    protected function getExchangeRateFromDatabase(string $fromCurrency, string $toCurrency): ?float
    {
        $fromCurrencyModel = Currency::where('code', $fromCurrency)->first();
        $toCurrencyModel = Currency::where('code', $toCurrency)->first();

        if (!$fromCurrencyModel || !$toCurrencyModel) {
            return null;
        }

        // Check if rates are recent (within 24 hours)
        $maxAge = now()->subHours(24);
        
        if ($fromCurrencyModel->exchange_rate_last_updated < $maxAge ||
            $toCurrencyModel->exchange_rate_last_updated < $maxAge) {
            return null;
        }

        // Calculate cross rate
        if ($fromCurrency === $this->baseCurrency) {
            return $toCurrencyModel->exchange_rate;
        } elseif ($toCurrency === $this->baseCurrency) {
            return 1 / $fromCurrencyModel->exchange_rate;
        } else {
            return $toCurrencyModel->exchange_rate / $fromCurrencyModel->exchange_rate;
        }
    }

    /**
     * Get exchange rate from external API.
     *
     * @param string $fromCurrency
     * @param string $toCurrency
     * @return float
     */
    protected function getExchangeRateFromAPI(string $fromCurrency, string $toCurrency): float
    {
        $rates = $this->fetchLatestRates($fromCurrency);
        
        if (isset($rates[$toCurrency])) {
            return $rates[$toCurrency];
        }

        // Fallback: try reverse conversion
        $reverseRates = $this->fetchLatestRates($toCurrency);
        
        if (isset($reverseRates[$fromCurrency])) {
            return 1 / $reverseRates[$fromCurrency];
        }

        // Last resort: use base currency as intermediary
        if ($fromCurrency !== $this->baseCurrency && $toCurrency !== $this->baseCurrency) {
            $fromToBase = $this->fetchLatestRates($fromCurrency)[$this->baseCurrency] ?? null;
            $baseToTo = $this->fetchLatestRates($this->baseCurrency)[$toCurrency] ?? null;
            
            if ($fromToBase && $baseToTo) {
                return $fromToBase * $baseToTo;
            }
        }

        Log::warning('Could not fetch exchange rate', [
            'from' => $fromCurrency,
            'to' => $toCurrency,
        ]);

        return 1.0; // Fallback to 1:1 rate
    }

    /**
     * Fetch latest rates from external API.
     *
     * @param string $baseCurrency
     * @return array
     */
    protected function fetchLatestRates(string $baseCurrency): array
    {
        $cacheKey = "latest_rates_{$baseCurrency}";
        
        return Cache::remember($cacheKey, 1800, function () use ($baseCurrency) {
            foreach ($this->providers as $provider => $baseUrl) {
                try {
                    $rates = $this->fetchFromProvider($provider, $baseUrl, $baseCurrency);
                    
                    if (!empty($rates)) {
                        return $rates;
                    }
                } catch (\Exception $e) {
                    Log::warning("Failed to fetch rates from {$provider}", [
                        'error' => $e->getMessage(),
                    ]);
                    continue;
                }
            }

            return [];
        });
    }

    /**
     * Fetch rates from specific provider.
     *
     * @param string $provider
     * @param string $baseUrl
     * @param string $baseCurrency
     * @return array
     */
    protected function fetchFromProvider(string $provider, string $baseUrl, string $baseCurrency): array
    {
        switch ($provider) {
            case 'exchangerate_api':
                $response = Http::timeout(10)->get($baseUrl . $baseCurrency);
                
                if ($response->successful()) {
                    $data = $response->json();
                    return $data['rates'] ?? [];
                }
                break;

            case 'fixer':
                $apiKey = config('services.fixer.key');
                
                if (!$apiKey) {
                    return [];
                }

                $response = Http::timeout(10)->get($baseUrl, [
                    'access_key' => $apiKey,
                    'base' => $baseCurrency,
                ]);
                
                if ($response->successful()) {
                    $data = $response->json();
                    return $data['rates'] ?? [];
                }
                break;

            case 'currencylayer':
                $apiKey = config('services.currencylayer.key');
                
                if (!$apiKey) {
                    return [];
                }

                $response = Http::timeout(10)->get($baseUrl, [
                    'access_key' => $apiKey,
                    'source' => $baseCurrency,
                ]);
                
                if ($response->successful()) {
                    $data = $response->json();
                    $quotes = $data['quotes'] ?? [];
                    
                    // Convert quotes format (USDEUR) to simple format (EUR)
                    $rates = [];
                    foreach ($quotes as $pair => $rate) {
                        $toCurrency = substr($pair, 3);
                        $rates[$toCurrency] = $rate;
                    }
                    
                    return $rates;
                }
                break;
        }

        return [];
    }

    /**
     * Get base currency.
     *
     * @return string
     */
    protected function getBaseCurrency(): string
    {
        return Currency::where('base_currency', true)->value('code') ?? 'USD';
    }
}
