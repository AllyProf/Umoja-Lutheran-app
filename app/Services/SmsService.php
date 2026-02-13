<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    private $username;
    private $password;
    private $from;
    private $baseUrl;

    public function __construct()
    {
        $this->username = env('SMS_USERNAME', 'emcatechn');
        $this->password = env('SMS_PASSWORD', 'Emca@#12');
        $this->from = env('SMS_FROM', 'MauzoLink');
        $this->baseUrl = env('SMS_BASE_URL', 'https://messaging-service.co.tz/link/sms/v1/text/single');
    }

    /**
     * Send SMS
     *
     * @param string $phoneNumber Phone number
     * @param string $message Message to send
     * @return array
     */
    public function sendSms($phoneNumber, $message)
    {
        // Ensure phone number starts with 255
        $phone_no = $this->formatPhoneNumber($phoneNumber);
        
        try {
            $response = Http::get($this->baseUrl, [
                'username' => $this->username,
                'password' => $this->password,
                'from' => $this->from,
                'to' => $phone_no,
                'text' => $message,
            ]);

            $httpCode = $response->status();
            
            return [
                'success' => $httpCode == 200,
                'response' => $response->body(),
                'http_code' => $httpCode
            ];
        } catch (\Exception $e) {
            Log::error('SMS Sending Error', [
                'error' => $e->getMessage(),
                'phone' => $phone_no
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'response' => null
            ];
        }
    }

    /**
     * Format phone number to start with 255
     *
     * @param string $phoneNumber
     * @return string
     */
    private function formatPhoneNumber($phoneNumber)
    {
        // Remove all non-digit characters
        $phone = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // If starts with 0, replace with 255
        if (substr($phone, 0, 1) == '0') {
            $phone = '255' . substr($phone, 1);
        }
        
        // If doesn't start with 255, add it
        if (substr($phone, 0, 3) != '255') {
            $phone = '255' . $phone;
        }
        
        return $phone;
    }
}
