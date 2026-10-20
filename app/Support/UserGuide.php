<?php

namespace App\Support;

class UserGuide
{
    /**
     * Resolve the user guide for the current (or given) route.
     */
    public static function forRoute(?string $routeName = null, ?string $path = null): array
    {
        $routeName = $routeName ?: (request()->route()?->getName() ?? '');
        $path = $path ?: trim(request()->path(), '/');
        $guides = config('user_guides.pages', []);

        if ($routeName && isset($guides[$routeName])) {
            return self::normalize($guides[$routeName], $routeName);
        }

        // Longest path-prefix match
        $prefixes = config('user_guides.path_prefixes', []);
        $best = null;
        $bestLen = -1;
        foreach ($prefixes as $prefix => $guide) {
            if ($prefix !== '' && str_starts_with($path, trim($prefix, '/'))) {
                $len = strlen($prefix);
                if ($len > $bestLen) {
                    $best = $guide;
                    $bestLen = $len;
                }
            }
        }
        if ($best) {
            return self::normalize($best, $routeName ?: $path);
        }

        return self::normalize(self::fallback($routeName, $path), $routeName ?: $path);
    }

    public static function normalize(array $guide, string $key): array
    {
        $title = $guide['title'] ?? self::titleFromKey($key);
        $steps = array_values($guide['steps'] ?? []);
        $normalizedSteps = [];
        foreach ($steps as $i => $step) {
            if (is_string($step)) {
                $normalizedSteps[] = [
                    'n' => $i + 1,
                    'text' => $step,
                ];
            } else {
                $normalizedSteps[] = [
                    'n' => (int) ($step['n'] ?? ($i + 1)),
                    'text' => (string) ($step['text'] ?? $step['body'] ?? ''),
                ];
            }
        }

        if ($normalizedSteps === []) {
            $normalizedSteps = [
                ['n' => 1, 'text' => 'Use the filters and search tools at the top of this page to find the records you need.'],
                ['n' => 2, 'text' => 'Select a row or action button in the main list to view details or complete a task.'],
            ];
        }

        $markers = $guide['markers'] ?? self::defaultMarkers(count($normalizedSteps));

        return [
            'key' => $key,
            'title' => $title,
            'breadcrumb' => $guide['breadcrumb'] ?? ['Dashboard', $title],
            'overview' => $guide['overview'] ?? "This screen helps you work with {$title}. Follow the numbered steps to complete common tasks.",
            'steps' => $normalizedSteps,
            'tip' => $guide['tip'] ?? 'If something looks wrong, refresh the page or ask a supervisor. Always finish open shifts and handovers before leaving.',
            'markers' => $markers,
        ];
    }

    protected static function fallback(string $routeName, string $path): array
    {
        $title = self::titleFromKey($routeName ?: $path);

        return [
            'title' => $title,
            'breadcrumb' => ['Dashboard', $title],
            'overview' => "User guide for {$title}. This page is part of the Umoja Lutheran Hotel & Restaurant system.",
            'steps' => [
                'Review the page title and summary cards (if any) to understand today’s status.',
                'Use the main table or form on this page to complete your task, then save or submit.',
            ],
            'tip' => 'Click the book icon anytime to reopen this guide for the menu you are on.',
        ];
    }

    protected static function titleFromKey(string $key): string
    {
        $key = preg_replace('/[#].*$/', '', $key) ?: $key;
        $parts = preg_split('/[.\-\/_]+/', $key) ?: [];
        $parts = array_values(array_filter($parts, fn ($p) => $p !== '' && !in_array($p, ['index', 'show', 'create', 'edit'], true)));
        $last = array_slice($parts, -2);
        $label = implode(' ', $last ?: ['Page']);

        return ucwords(str_replace('_', ' ', $label));
    }

    protected static function defaultMarkers(int $count): array
    {
        $positions = [
            ['top' => '18%', 'left' => '78%'],
            ['top' => '42%', 'left' => '12%'],
            ['top' => '62%', 'left' => '55%'],
            ['top' => '78%', 'left' => '30%'],
        ];
        $markers = [];
        for ($i = 0; $i < min($count, count($positions)); $i++) {
            $markers[] = array_merge(['n' => $i + 1], $positions[$i]);
        }

        return $markers;
    }
}
