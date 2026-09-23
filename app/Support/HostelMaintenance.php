<?php

namespace App\Support;

class HostelMaintenance
{
    public static function path(): string
    {
        return storage_path('framework/hostel-maintenance.json');
    }

    public static function message(): string
    {
        $message = trim(self::data()['message']);

        return $message !== ''
            ? self::data()['message']
            : 'System is under maintenance. Please check back later.';
    }

    public static function pageResponse()
    {
        return response()
            ->view('maintenance', ['message' => self::message()], 503)
            ->header('Retry-After', '60');
    }

    public static function data(): array
    {
        $path = self::path();
        if (!is_file($path)) {
            return ['enabled' => false, 'message' => ''];
        }

        $data = json_decode((string) file_get_contents($path), true);
        if (!is_array($data)) {
            return ['enabled' => false, 'message' => ''];
        }

        return [
            'enabled' => !empty($data['enabled']),
            'message' => (string) ($data['message'] ?? ''),
        ];
    }

    public static function enable(string $message): void
    {
        $dir = dirname(self::path());
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents(self::path(), json_encode([
            'enabled' => true,
            'message' => $message,
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        self::clearLaravelDownFiles();
    }

    public static function disable(): void
    {
        $path = self::path();
        if (is_file($path)) {
            unlink($path);
        }

        self::clearLaravelDownFiles();
    }

    private static function clearLaravelDownFiles(): void
    {
        foreach ([
            storage_path('framework/down'),
            storage_path('framework/maintenance.php'),
        ] as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
}
