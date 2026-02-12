<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CurrencyExchangeService
{
    // Using exchangerate-api.com - free API that supports TZS (Tanzanian Shilling)
    private const API_URL = 'https://api.exchangerate-api.com/v4/latest/USD';
    private const CACHE_KEY = 'currency_exchange_rate_usd_to_tsh';
    private const CACHE_DURATION = 3600; // 1 hour cache

    /**
     * Get exchange rate from USD to TZS
     * Priority: API (cached) > API (fresh) > Hotel Settings > Default
     * 
     * @return float|null
     */
    public function getUsdToTshRate(): ?float
    {
        // 1. Check if manager has set a manual override in Hotel Settings (Top Priority)
        if (class_exists(\App\Models\HotelSetting::class)) {
            $configuredRate = \App\Models\HotelSetting::getValue('exchange_rate_usd_to_tzs');
            // If the manual rate is set to something reasonable (not 0 or default fallback 2455)
            // we assume the user wants to use THIS specific rate.
            if ($configuredRate && is_numeric($configuredRate) && $configuredRate > 0) {
                return (float) $configuredRate;
            }
        }

        // 2. Try to get from cache (API data)
        $cachedRate = Cache::get(self::CACHE_KEY);
        if ($cachedRate !== null) {
            return (float) $cachedRate;
        }

        // 3. Fetch from fresh API
        try {
            // Fetch from exchangerate-api.com - free API that supports TZS
            $response = Http::timeout(5)->get(self::API_URL);

            if ($response->successful()) {
                $data = $response->json();
                $rate = $data['rates']['TZS'] ?? null;

                if ($rate && is_numeric($rate) && $rate > 0) {
                    // Cache the rate from API
                    Cache::put(self::CACHE_KEY, $rate, self::CACHE_DURATION);
                    Log::info('Exchange rate fetched from API (exchangerate-api.com): ' . $rate);
                    return (float) $rate;
                }
            }

            Log::warning('Failed to fetch exchange rate from exchangerate-api.com', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching exchange rate from API: ' . $e->getMessage());
        }

        // 4. Last resort: return default rate
        return $this->getDefaultRate();
    }

    /**
     * Convert TZS to USD
     * 
     * @param float $tshAmount
     * @return float
     */
    public function convertTshToUsd(float $tshAmount): float
    {
        $rate = $this->getUsdToTshRate();
        if (!$rate || $rate <= 0) {
            return 0;
        }
        return round($tshAmount / $rate, 2);
    }

    /**
     * Convert USD to TZS
     * 
     * @param float $usdAmount
     * @return float
     */
    public function convertUsdToTsh(float $usdAmount): float
    {
        $rate = $this->getUsdToTshRate();
        if (!$rate || $rate <= 0) {
            return 0;
        }
        return round($usdAmount * $rate, 2);
    }

    /**
     * Get default exchange rate (fallback only - should rarely be used)
     * 
     * @return float
     */
    private function getDefaultRate(): float
    {
        // Default rate: 1 USD = 2455 TZS (fallback only, can be configured in .env)
        // This should only be used if API fails and no hotel setting is configured
        $defaultRate = env('EXCHANGE_RATE_USD_TO_TSH', 2455);
        Log::warning('Using default exchange rate fallback: ' . $defaultRate);
        return (float) $defaultRate;
    }

    /**
     * Get historical exchange rates for trend analysis
     * 
     * @param int $days Number of days to fetch (default: 30)
     * @return array
     */
    public function getHistoricalRates(int $days = 30): array
    {
        $cacheKey = 'currency_exchange_rate_history_' . $days;
        $cachedRates = Cache::get($cacheKey);
        
        if ($cachedRates !== null) {
            return $cachedRates;
        }

        try {
            $endDate = now()->format('Y-m-d');
            $startDate = now()->subDays($days)->format('Y-m-d');
            
            // Fetch historical data from Frankfurter API
            $response = Http::timeout(10)->get('https://api.frankfurter.app/' . $startDate . '..' . $endDate, [
                'from' => 'USD',
                'to' => 'TZS'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $rates = [];
                
                if (isset($data['rates']) && is_array($data['rates'])) {
                    foreach ($data['rates'] as $date => $rateData) {
                        if (isset($rateData['TZS'])) {
                            $rates[] = [
                                'date' => $date,
                                'rate' => (float) $rateData['TZS'],
                                'timestamp' => strtotime($date)
                            ];
                        }
                    }
                }
                
                // Sort by date
                usort($rates, function($a, $b) {
                    return $a['timestamp'] <=> $b['timestamp'];
                });
                
                // Cache for 1 hour
                Cache::put($cacheKey, $rates, 3600);
                
                return $rates;
            }
        } catch (\Exception $e) {
            Log::error('Error fetching historical exchange rates: ' . $e->getMessage());
        }

        // Return empty array if API fails
        return [];
    }

    /**
     * Get exchange rate statistics
     * 
     * @param int $days Number of days to analyze
     * @return array
     */
    public function getExchangeRateStats(int $days = 30): array
    {
        $historicalRates = $this->getHistoricalRates($days);
        
        if (empty($historicalRates)) {
            return [
                'current' => $this->getUsdToTshRate(),
                'average' => $this->getUsdToTshRate(),
                'min' => $this->getUsdToTshRate(),
                'max' => $this->getUsdToTshRate(),
                'change' => 0,
                'change_percent' => 0,
            ];
        }
        
        $rates = array_column($historicalRates, 'rate');
        $currentRate = $this->getUsdToTshRate();
        $oldestRate = $historicalRates[0]['rate'] ?? $currentRate;
        
        return [
            'current' => $currentRate,
            'average' => round(array_sum($rates) / count($rates), 2),
            'min' => round(min($rates), 2),
            'max' => round(max($rates), 2),
            'change' => round($currentRate - $oldestRate, 2),
            'change_percent' => $oldestRate > 0 ? round((($currentRate - $oldestRate) / $oldestRate) * 100, 2) : 0,
        ];
    }

    /**
     * Clear cached exchange rate
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Clear historical rates cache
     */
    public function clearHistoryCache(int $days = null): void
    {
        if ($days) {
            Cache::forget('currency_exchange_rate_history_' . $days);
        } else {
            // Clear all history caches (7, 30, 90, 180, 365 days)
            foreach ([7, 30, 90, 180, 365] as $day) {
                Cache::forget('currency_exchange_rate_history_' . $day);
            }
        }
    }
}



