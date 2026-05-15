<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class LimitBackups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backups:limit {count=3}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limit the number of backups on Google Drive to a specific count';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = (int) $this->argument('count');
        $disk = 'google';
        $backupName = config('backup.backup.name');

        if (!$backupName) {
            $this->error('Backup name not configured in config/backup.php');
            return 1;
        }

        $this->info("Cleaning up backups for: $backupName (Limit: $limit)");

        try {
            // Get all files from the backup directory on Google Drive
            // spatie/laravel-backup stores backups in a folder named after the app name
            $allFiles = Storage::disk($disk)->allFiles($backupName);
            
            // Filter only zip files (standard for spatie/laravel-backup)
            $backupFiles = array_filter($allFiles, function($file) {
                return str_ends_with($file, '.zip');
            });

            $count = count($backupFiles);
            if ($count <= $limit) {
                $this->info("Number of backups ($count) is already within the limit.");
                return 0;
            }

            // Get file details with timestamps to ensure we keep the NEWEST ones
            $filesWithTime = [];
            foreach ($backupFiles as $file) {
                try {
                    $filesWithTime[$file] = Storage::disk($disk)->lastModified($file);
                } catch (\Exception $e) {
                    // Fallback to filename if timestamp fails (usually filenames have dates)
                    $filesWithTime[$file] = $file; 
                }
            }

            // Sort by value descending (newest timestamp or alphabetical filename)
            arsort($filesWithTime);

            $filesToKeep = array_slice(array_keys($filesWithTime), 0, $limit);
            $filesToDelete = array_slice(array_keys($filesWithTime), $limit);

            foreach ($filesToDelete as $file) {
                $this->info("Deleting old backup: $file");
                Storage::disk($disk)->delete($file);
            }

            $this->info("Cleanup completed. Kept " . count($filesToKeep) . " files, deleted " . count($filesToDelete) . " files.");
            Log::info("Backup Cleanup: Kept " . count($filesToKeep) . " files, deleted " . count($filesToDelete) . " files.");
            
        } catch (\Exception $e) {
            $this->error("Error during backup cleanup: " . $e->getMessage());
            Log::error("Backup Cleanup Error: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
