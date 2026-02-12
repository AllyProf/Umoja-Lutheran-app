<?php

namespace App\Console\Commands;

use App\Models\Guest;
use Illuminate\Console\Command;

class CheckGuestEmail extends Command
{
    protected $signature = 'check:guest-email {email}';
    protected $description = 'Check if an email exists in the guests table';

    public function handle()
    {
        $email = $this->argument('email');
        
        $guest = Guest::where('email', $email)->first();
        
        if ($guest) {
            $this->info("✓ Email EXISTS in guests table");
            $this->line("ID: {$guest->id}");
            $this->line("Name: {$guest->name}");
            $this->line("Email: {$guest->email}");
            $this->line("Active: " . ($guest->is_active ? 'Yes' : 'No'));
            $this->line("Created: {$guest->created_at}");
        } else {
            $this->error("✗ Email NOT FOUND in guests table");
            $this->line("Email: {$email}");
        }
        
        return 0;
    }
}



