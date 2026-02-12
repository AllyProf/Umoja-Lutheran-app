<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Room;

class SyncStorageFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:sync-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync storage files to public directory (for Windows compatibility)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Syncing storage files to public directory...');
        
        // Get all rooms with images
        $rooms = Room::whereNotNull('images')->get();
        $syncedFiles = 0;
        $skippedFiles = 0;

        foreach ($rooms as $room) {
            if ($room->images && is_array($room->images)) {
                foreach ($room->images as $img) {
                    $imgPath = trim($img);
                    $publicPath = public_path('storage/' . $imgPath);
                    $storagePath = storage_path('app/public/' . $imgPath);
                    
                    // Check if file exists in storage but not in public
                    if (file_exists($storagePath) && !file_exists($publicPath)) {
                        // Create directory if it doesn't exist
                        $publicDir = dirname($publicPath);
                        if (!is_dir($publicDir)) {
                            mkdir($publicDir, 0755, true);
                        }
                        
                        // Copy the file
                        if (copy($storagePath, $publicPath)) {
                            $syncedFiles++;
                            $this->line("Synced: $imgPath");
                        } else {
                            $this->error("Failed to sync: $imgPath");
                        }
                    } else {
                        $skippedFiles++;
                    }
                }
            }
        }

        if ($syncedFiles > 0) {
            $this->info("Successfully synced $syncedFiles file(s).");
        } else {
            $this->info("All files are already synced!");
        }
        
        return 0;
    }
}
