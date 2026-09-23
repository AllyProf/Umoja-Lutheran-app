<?php

namespace App\Services;

/**
 * System currency is TSh only. Exchange-rate helpers are no-ops retained for
 * backward-compatible call sites that previously converted USD ↔ TZS.
 */
class CurrencyExchangeService
{
    /**
     * Always 1 — amounts in this system are already TSh.
     */
    public function getUsdToTshRate(): float
    {
        return 1.0;
    }

    /**
     * No conversion — amount is already TSh.
     */
    public function convertTshToUsd(float $tshAmount): float
    {
        return round($tshAmount, 2);
    }

    /**
     * No conversion — amount is already TSh.
     */
    public function convertUsdToTsh(float $usdAmount): float
    {
        return round($usdAmount, 2);
    }

    public function getHistoricalRates(int $days = 30): array
    {
        return [];
    }

    public function getExchangeRateStats(int $days = 30): array
    {
        return [
            'current' => 1.0,
            'average' => 1.0,
            'min' => 1.0,
            'max' => 1.0,
            'change' => 0,
            'change_percent' => 0,
        ];
    }

    public function clearCache(): void
    {
        // no-op
    }

    public function clearHistoryCache(int $days = null): void
    {
        // no-op
    }
}
