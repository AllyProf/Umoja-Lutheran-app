<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
    ];

    /**
     * Get a setting value by key
     */
    public static function getValue($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value by key
     */
    public static function setValue($key, $value)
    {
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    /**
     * Get WiFi password
     */
    public static function getWifiPassword()
    {
        return self::getValue('wifi_password');
    }

    /**
     * Get WiFi network name
     */
    public static function getWifiNetworkName()
    {
        return self::getValue('wifi_network_name', 'PrimeLand_Hotel');
    }
}





