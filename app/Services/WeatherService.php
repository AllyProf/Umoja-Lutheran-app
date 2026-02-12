<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WeatherService
{
    private $apiKey;
    private $baseUrl = 'https://api.openweathermap.org/data/2.5';
    
    // Moshi, Tanzania coordinates
    private $defaultLat = -3.3344;
    private $defaultLon = 37.3404;
    private $defaultCity = 'Moshi';
    private $defaultCountry = 'TZ';
    
    public function __construct()
    {
        // Use config() instead of env() for better performance and to avoid caching issues
        $this->apiKey = config('services.openweather.api_key', env('OPENWEATHER_API_KEY', ''));
    }
    
    /**
     * Get current weather for a location
     */
    public function getCurrentWeather($lat = null, $lon = null, $city = null)
    {
        $lat = $lat ?? $this->defaultLat;
        $lon = $lon ?? $this->defaultLon;
        $city = $city ?? $this->defaultCity;
        
        // Cache for 10 minutes - use a fixed key that changes every 10 minutes
        // Include location in cache key for location-specific caching
        $minutes = floor(now()->timestamp / 600) * 600; // Round to nearest 10 minutes
        if ($lat && $lon && abs($lat - $this->defaultLat) > 0.1) {
            // Use coordinates for cache key if custom location
            $cacheKey = "weather_" . round($lat, 2) . "_" . round($lon, 2) . "_" . $minutes;
        } else {
            // Use city name for default location
            $cacheKey = "weather_" . strtolower($city) . "_" . $minutes;
        }
        
        return Cache::remember($cacheKey, 600, function () use ($lat, $lon, $city) {
            try {
                // Check if API key is set
                if (empty($this->apiKey)) {
                    Log::warning('OpenWeatherMap API key not configured', [
                        'config_value' => config('services.openweather.api_key'),
                        'env_value' => env('OPENWEATHER_API_KEY')
                    ]);
                    return null;
                }
                
                Log::info('Fetching weather', [
                    'api_key_length' => strlen($this->apiKey),
                    'location' => ['lat' => $lat, 'lon' => $lon, 'city' => $city]
                ]);
                
                // Try by coordinates first if provided (more accurate)
                $url = $this->baseUrl . '/weather';
                
                if ($lat && $lon && abs($lat) <= 90 && abs($lon) <= 180) {
                    // Use coordinates if valid
                    $params = [
                        'lat' => $lat,
                        'lon' => $lon,
                        'appid' => $this->apiKey,
                        'units' => 'metric',
                    ];
                    
                    $response = Http::timeout(10)->get($url, $params);
                    
                    if ($response->successful()) {
                        $data = $response->json();
                        return $this->formatWeatherData($data);
                    }
                }
                
                // Fallback to city name search
                $params = [
                    'q' => $city,
                    'appid' => $this->apiKey,
                    'units' => 'metric', // Celsius
                ];
                
                $response = Http::timeout(10)->get($url, $params);
                
                if ($response->successful()) {
                    $data = $response->json();
                    return $this->formatWeatherData($data);
                }
                
                // Log the error for debugging
                $errorBody = $response->json();
                $isInvalidKey = $response->status() === 401 || (isset($errorBody['cod']) && $errorBody['cod'] == 401);
                
                Log::warning('Weather API request failed (city search)', [
                    'url' => $url,
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'api_key_set' => !empty($this->apiKey),
                    'is_invalid_key' => $isInvalidKey,
                    'location' => ['lat' => $lat, 'lon' => $lon, 'city' => $city]
                ]);
                
                // If it's an invalid key error, return a specific error structure
                if ($isInvalidKey) {
                    return [
                        'error' => 'invalid_api_key',
                        'message' => 'The OpenWeatherMap API key is invalid or not activated. Please check your API key in the .env file and ensure it is activated in your OpenWeatherMap account.'
                    ];
                }
                
                // Final fallback to coordinates if city search fails and we have coordinates
                if ($lat && $lon && abs($lat) <= 90 && abs($lon) <= 180) {
                    $params = [
                        'lat' => $lat,
                        'lon' => $lon,
                        'appid' => $this->apiKey,
                        'units' => 'metric',
                    ];
                    
                    $response = Http::timeout(10)->get($url, $params);
                
                    if ($response->successful()) {
                        $data = $response->json();
                        return $this->formatWeatherData($data);
                    }
                    
                    $errorBody = $response->json();
                    $isInvalidKey = $response->status() === 401 || (isset($errorBody['cod']) && $errorBody['cod'] == 401);
                    
                    Log::warning('Weather API request failed (coordinates fallback)', [
                        'url' => $url,
                        'status' => $response->status(),
                        'body' => $response->body(),
                        'api_key_set' => !empty($this->apiKey),
                        'is_invalid_key' => $isInvalidKey
                    ]);
                    
                    // If it's an invalid key error, return a specific error structure
                    if ($isInvalidKey) {
                        return [
                            'error' => 'invalid_api_key',
                            'message' => 'The OpenWeatherMap API key is invalid or not activated. Please check your API key in the .env file and ensure it is activated in your OpenWeatherMap account.'
                        ];
                    }
                }
                
                return null;
            } catch (\Exception $e) {
                Log::error('Weather API error: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString(),
                    'api_key_set' => !empty($this->apiKey)
                ]);
                return null;
            }
        });
    }
    
    /**
     * Get weather forecast (5-day forecast)
     */
    public function getForecast($lat = null, $lon = null, $city = null)
    {
        $lat = $lat ?? $this->defaultLat;
        $lon = $lon ?? $this->defaultLon;
        $city = $city ?? $this->defaultCity;
        
        // Cache for 30 minutes - use a fixed key that changes every 30 minutes
        // Include location in cache key for location-specific caching
        $minutes = floor(now()->timestamp / 1800) * 1800; // Round to nearest 30 minutes
        if ($lat && $lon && abs($lat - $this->defaultLat) > 0.1) {
            // Use coordinates for cache key if custom location
            $cacheKey = "weather_forecast_" . round($lat, 2) . "_" . round($lon, 2) . "_" . $minutes;
        } else {
            // Use city name for default location
            $cacheKey = "weather_forecast_" . strtolower($city) . "_" . $minutes;
        }
        
        return Cache::remember($cacheKey, 1800, function () use ($lat, $lon, $city) {
            try {
                // Check if API key is set
                if (empty($this->apiKey)) {
                    Log::warning('OpenWeatherMap API key not configured');
                    return null;
                }
                
                $url = $this->baseUrl . '/forecast';
                
                // Try by coordinates first if provided (more accurate)
                if ($lat && $lon && abs($lat) <= 90 && abs($lon) <= 180) {
                    $params = [
                        'lat' => $lat,
                        'lon' => $lon,
                        'appid' => $this->apiKey,
                        'units' => 'metric',
                        'cnt' => 5, // 5 days
                    ];
                    
                    $response = Http::timeout(10)->get($url, $params);
                    
                    if ($response->successful()) {
                        $data = $response->json();
                        return $this->formatForecastData($data);
                    }
                }
                
                // Fallback to city name
                $params = [
                    'q' => $city,
                    'appid' => $this->apiKey,
                    'units' => 'metric',
                    'cnt' => 5, // 5 days
                ];
                
                $response = Http::timeout(10)->get($url, $params);
                
                if ($response->successful()) {
                    $data = $response->json();
                    return $this->formatForecastData($data);
                }
                
                // Final fallback to coordinates if city search fails
                if ($lat && $lon && abs($lat) <= 90 && abs($lon) <= 180) {
                    $params = [
                        'lat' => $lat,
                        'lon' => $lon,
                        'appid' => $this->apiKey,
                        'units' => 'metric',
                        'cnt' => 5,
                    ];
                    
                    $response = Http::timeout(10)->get($url, $params);
                
                    if ($response->successful()) {
                        $data = $response->json();
                        return $this->formatForecastData($data);
                    }
                }
                
                return null;
            } catch (\Exception $e) {
                Log::error('Weather forecast API error: ' . $e->getMessage());
                return null;
            }
        });
    }
    
    /**
     * Format weather data for display
     */
    private function formatWeatherData($data)
    {
        if (!isset($data['main']) || !isset($data['weather'][0])) {
            return null;
        }
        
        return [
            'city' => $data['name'] ?? 'Moshi',
            'country' => $data['sys']['country'] ?? 'TZ',
            'temperature' => round($data['main']['temp']),
            'feels_like' => round($data['main']['feels_like']),
            'temp_min' => round($data['main']['temp_min']),
            'temp_max' => round($data['main']['temp_max']),
            'humidity' => $data['main']['humidity'],
            'pressure' => $data['main']['pressure'],
            'description' => ucfirst($data['weather'][0]['description']),
            'icon' => $data['weather'][0]['icon'],
            'wind_speed' => isset($data['wind']['speed']) ? round($data['wind']['speed'] * 3.6, 1) : 0, // Convert m/s to km/h
            'wind_direction' => isset($data['wind']['deg']) ? $this->getWindDirection($data['wind']['deg']) : 'N/A',
            'visibility' => isset($data['visibility']) ? round($data['visibility'] / 1000, 1) : null, // Convert to km
            'sunrise' => isset($data['sys']['sunrise']) ? date('H:i', $data['sys']['sunrise']) : null,
            'sunset' => isset($data['sys']['sunset']) ? date('H:i', $data['sys']['sunset']) : null,
            'updated_at' => now()->format('H:i'),
        ];
    }
    
    /**
     * Format forecast data
     */
    private function formatForecastData($data)
    {
        if (!isset($data['list']) || !is_array($data['list'])) {
            return [];
        }
        
        $forecast = [];
        foreach ($data['list'] as $item) {
            $forecast[] = [
                'date' => date('Y-m-d', $item['dt']),
                'time' => date('H:i', $item['dt']),
                'temperature' => round($item['main']['temp']),
                'temp_min' => round($item['main']['temp_min']),
                'temp_max' => round($item['main']['temp_max']),
                'description' => ucfirst($item['weather'][0]['description']),
                'icon' => $item['weather'][0]['icon'],
                'humidity' => $item['main']['humidity'],
                'wind_speed' => isset($item['wind']['speed']) ? round($item['wind']['speed'] * 3.6, 1) : 0,
            ];
        }
        
        return $forecast;
    }
    
    /**
     * Get wind direction from degrees
     */
    private function getWindDirection($degrees)
    {
        $directions = ['N', 'NNE', 'NE', 'ENE', 'E', 'ESE', 'SE', 'SSE', 'S', 'SSW', 'SW', 'WSW', 'W', 'WNW', 'NW', 'NNW'];
        $index = round($degrees / 22.5) % 16;
        return $directions[$index];
    }
    
    /**
     * Get weather icon class based on OpenWeatherMap icon code
     */
    public function getWeatherIconClass($icon)
    {
        $iconMap = [
            '01d' => 'fa-sun',           // clear sky day
            '01n' => 'fa-moon',          // clear sky night
            '02d' => 'fa-cloud-sun',     // few clouds day
            '02n' => 'fa-cloud-moon',    // few clouds night
            '03d' => 'fa-cloud',         // scattered clouds
            '03n' => 'fa-cloud',
            '04d' => 'fa-cloud',        // broken clouds
            '04n' => 'fa-cloud',
            '09d' => 'fa-tint',          // shower rain
            '09n' => 'fa-tint',
            '10d' => 'fa-cloud-rain',    // rain day
            '10n' => 'fa-cloud-rain',    // rain night
            '11d' => 'fa-bolt',          // thunderstorm
            '11n' => 'fa-bolt',
            '13d' => 'fa-snowflake-o',   // snow
            '13n' => 'fa-snowflake-o',
            '50d' => 'fa-fog',           // mist
            '50n' => 'fa-fog',
        ];
        
        return $iconMap[$icon] ?? 'fa-cloud';
    }
}

