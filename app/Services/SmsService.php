<?php

namespace App\Services;

use App\Models\HotelSetting;
use App\Models\SmsLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * SMS feature toggles controlled by Super Admin.
     * Keys are stored in hotel_settings as "1" (on) or "0" (off).
     */
    public const FEATURES = [
        'sms_otp' => [
            'label' => 'Login OTP',
            'description' => 'One-time passwords for staff/guest login. Turning off may block SMS login.',
            'group' => 'Auth & Accounts',
            'warning' => true,
            'default' => true,
        ],
        'sms_password_reset' => [
            'label' => 'Password Reset',
            'description' => 'Forgot-password and admin password-reset SMS.',
            'group' => 'Auth & Accounts',
            'warning' => false,
            'default' => true,
        ],
        'sms_user_welcome' => [
            'label' => 'New User Welcome',
            'description' => 'Credentials SMS when Super Admin creates a staff account.',
            'group' => 'Auth & Accounts',
            'warning' => false,
            'default' => true,
        ],
        'sms_booking_guest' => [
            'label' => 'Booking → Guest / Company / Guider',
            'description' => 'Booking confirmations, status updates, reminders, extensions, corporate guest SMS.',
            'group' => 'Bookings',
            'warning' => false,
            'default' => true,
        ],
        'sms_booking_staff' => [
            'label' => 'Booking → Staff / Managers',
            'description' => 'New booking & status alerts to reception, managers, and departments. Often high volume.',
            'group' => 'Bookings',
            'warning' => false,
            'default' => true,
        ],
        'sms_checkin_checkout_guest' => [
            'label' => 'Check-in / Check-out → Guest',
            'description' => 'Welcome + WiFi on check-in and thank-you on check-out.',
            'group' => 'Check-in / Check-out',
            'warning' => false,
            'default' => true,
        ],
        'sms_checkin_checkout_staff' => [
            'label' => 'Check-in / Check-out → Staff',
            'description' => 'Manager alerts and housekeeper cleaning notifications.',
            'group' => 'Check-in / Check-out',
            'warning' => false,
            'default' => true,
        ],
        'sms_payment_guest' => [
            'label' => 'Payment → Guest',
            'description' => 'Payment receipt SMS to guests (PayPal, POS, checkout).',
            'group' => 'Payments',
            'warning' => false,
            'default' => true,
        ],
        'sms_payment_staff' => [
            'label' => 'Payment → Staff',
            'description' => 'Payment received alerts to managers.',
            'group' => 'Payments',
            'warning' => false,
            'default' => true,
        ],
        'sms_service_request' => [
            'label' => 'Service Requests',
            'description' => 'New service request alerts and guest status updates.',
            'group' => 'Operations',
            'warning' => false,
            'default' => true,
        ],
        'sms_purchase_request' => [
            'label' => 'Purchase Requests',
            'description' => 'Urgent/emergency purchase alerts and approve/reject SMS to requesters.',
            'group' => 'Operations',
            'warning' => false,
            'default' => true,
        ],
        'sms_issues_housekeeping' => [
            'label' => 'Issues & Housekeeping',
            'description' => 'Room/guest issue reports and housekeeping alerts.',
            'group' => 'Operations',
            'warning' => false,
            'default' => true,
        ],
        'sms_backup_alerts' => [
            'label' => 'Backup Alerts',
            'description' => 'Database backup success/failure SMS.',
            'group' => 'System',
            'warning' => false,
            'default' => true,
        ],
        'sms_announcement' => [
            'label' => 'System Announcements',
            'description' => 'Optional SMS when Super Admin publishes a scrolling notice and chooses to text staff.',
            'group' => 'System',
            'warning' => false,
            'default' => true,
        ],
    ];

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
     * Whether a feature toggle is currently enabled.
     */
    public static function isFeatureEnabled(?string $featureKey): bool
    {
        if (!$featureKey || !isset(self::FEATURES[$featureKey])) {
            // Unknown feature: allow send (backward compatible) but prefer explicit keys.
            return true;
        }

        $default = self::FEATURES[$featureKey]['default'] ? '1' : '0';

        return HotelSetting::getValue($featureKey, $default) === '1';
    }

    /**
     * Current on/off map for all features (for Super Admin UI).
     */
    public static function featureStates(): array
    {
        $states = [];
        foreach (self::FEATURES as $key => $meta) {
            $states[$key] = self::isFeatureEnabled($key);
        }

        return $states;
    }

    /**
     * Features grouped for the settings UI.
     */
    public static function featuresByGroup(): array
    {
        $groups = [];
        foreach (self::FEATURES as $key => $meta) {
            $groups[$meta['group']][$key] = $meta;
        }

        return $groups;
    }

    /**
     * Send SMS (respects Super Admin feature toggles).
     *
     * @param string $phoneNumber Phone number
     * @param string $message Message to send
     * @param string|null $feature Feature key (e.g. sms_booking_staff) or legacy context label
     * @return array
     */
    public function sendSms($phoneNumber, $message, $feature = null)
    {
        $featureKey = $this->resolveFeatureKey($feature);

        // Ensure phone number starts with 255
        $phone_no = $this->formatPhoneNumber($phoneNumber);

        if (!self::isFeatureEnabled($featureKey)) {
            Log::info('SMS skipped — feature disabled', [
                'feature' => $featureKey,
                'phone' => $phone_no,
            ]);

            return [
                'success' => false,
                'skipped' => true,
                'feature' => $featureKey,
                'error' => 'SMS feature disabled by Super Admin',
                'response' => null,
            ];
        }

        try {
            $response = Http::get($this->baseUrl, [
                'username' => $this->username,
                'password' => $this->password,
                'from' => $this->from,
                'to' => $phone_no,
                'text' => $message,
            ]);

            $httpCode = $response->status();
            $result = [
                'success' => $httpCode == 200,
                'response' => $response->body(),
                'http_code' => $httpCode,
                'feature' => $featureKey,
            ];

            $this->logSms($phone_no, $message, $result, null, $featureKey);

            return $result;
        } catch (\Exception $e) {
            Log::error('SMS Sending Error', [
                'error' => $e->getMessage(),
                'phone' => $phone_no,
                'feature' => $featureKey,
            ]);

            $result = [
                'success' => false,
                'error' => $e->getMessage(),
                'response' => null,
                'feature' => $featureKey,
            ];

            $this->logSms($phone_no, $message, $result, $e->getMessage(), $featureKey);

            return $result;
        }
    }

    /**
     * Normalize caller-provided feature / context to a known toggle key.
     */
    private function resolveFeatureKey(?string $feature): ?string
    {
        if ($feature && isset(self::FEATURES[$feature])) {
            return $feature;
        }

        // Legacy: caller passed a controller class basename — map common ones.
        $caller = $feature ?: $this->guessContext();
        $map = [
            'AuthController' => 'sms_otp',
            'EmergencyAuthController' => 'sms_otp',
            'PasswordResetController' => 'sms_password_reset',
            'PaymentController' => 'sms_payment_guest',
            'ServiceRequestController' => 'sms_service_request',
            'PurchaseRequestController' => 'sms_purchase_request',
            'IssueReportController' => 'sms_issues_housekeeping',
            'HousekeeperController' => 'sms_issues_housekeeping',
            'AppServiceProvider' => 'sms_backup_alerts',
        ];

        return $map[$caller] ?? $feature;
    }

    /**
     * Persist SMS attempt for Super Admin usage tracking.
     */
    private function logSms(string $phone, string $message, array $result, ?string $error = null, ?string $context = null): void
    {
        SmsLog::record([
            'phone' => $phone,
            'message' => $message,
            'success' => (bool) ($result['success'] ?? false),
            'http_code' => $result['http_code'] ?? null,
            'response' => isset($result['response']) ? substr((string) $result['response'], 0, 2000) : null,
            'error' => $error ?? ($result['error'] ?? null),
            'sender' => $this->from,
            'context' => $context ?: $this->guessContext(),
        ]);
    }

    /**
     * Best-effort caller context when none is provided.
     */
    private function guessContext(): ?string
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 8);
        foreach ($trace as $frame) {
            $class = $frame['class'] ?? null;
            if (!$class || str_contains($class, 'SmsService')) {
                continue;
            }
            return class_basename($class);
        }

        return null;
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
