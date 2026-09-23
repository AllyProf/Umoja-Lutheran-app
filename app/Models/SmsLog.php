<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    protected $fillable = [
        'phone',
        'message',
        'success',
        'http_code',
        'response',
        'error',
        'sender',
        'context',
    ];

    protected $casts = [
        'success' => 'boolean',
        'http_code' => 'integer',
    ];

    /**
     * Record an SMS send attempt.
     */
    public static function record(array $data): ?self
    {
        try {
            return self::create($data);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Failed to record SMS log', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Usage counts for today, this week, and this month.
     */
    public static function usageStats(): array
    {
        return [
            'today' => self::whereDate('created_at', Carbon::today())->count(),
            'today_success' => self::whereDate('created_at', Carbon::today())->where('success', true)->count(),
            'today_failed' => self::whereDate('created_at', Carbon::today())->where('success', false)->count(),
            'this_week' => self::whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek(),
            ])->count(),
            'this_week_success' => self::whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek(),
            ])->where('success', true)->count(),
            'this_month' => self::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count(),
            'this_month_success' => self::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->where('success', true)
                ->count(),
            'total' => self::count(),
        ];
    }

    /**
     * Daily SMS counts for the last N days (inclusive of today).
     */
    public static function dailyBreakdown(int $days = 14): array
    {
        $start = Carbon::today()->subDays($days - 1);
        $rows = self::selectRaw('DATE(created_at) as day, COUNT(*) as total, SUM(success) as successful')
            ->where('created_at', '>=', $start->copy()->startOfDay())
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->keyBy('day');

        $breakdown = [];
        for ($i = 0; $i < $days; $i++) {
            $date = $start->copy()->addDays($i);
            $key = $date->toDateString();
            $row = $rows->get($key);
            $breakdown[] = [
                'date' => $key,
                'label' => $date->format('M j'),
                'total' => (int) ($row->total ?? 0),
                'successful' => (int) ($row->successful ?? 0),
            ];
        }

        return $breakdown;
    }
}
