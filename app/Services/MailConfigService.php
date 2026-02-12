<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;

class MailConfigService
{
    /**
     * Configure mail settings from .env file
     * This ensures mail uses .env configuration with proper SSL settings for port 465
     */
    public static function configure()
    {
        // Ensure proper encryption for port 465 (SSL) vs port 587 (TLS)
        $port = env('MAIL_PORT', 465);
        $encryption = env('MAIL_ENCRYPTION');
        
        // If port is 465 and encryption is not set, default to 'ssl'
        if ($port == 465 && empty($encryption)) {
            Config::set('mail.mailers.smtp.encryption', 'ssl');
        }
        // If port is 587 and encryption is not set, default to 'tls'
        elseif ($port == 587 && empty($encryption)) {
            Config::set('mail.mailers.smtp.encryption', 'tls');
        }
        
        // Ensure timeout is reasonable
        Config::set('mail.mailers.smtp.timeout', env('MAIL_TIMEOUT', 30));
    }
}

