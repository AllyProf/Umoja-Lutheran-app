<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\WeatherService;

class LocalInfoController extends Controller
{
    protected $weatherService;
    
    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }
    
    /**
     * Show local information page
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get location from request (lat, lon, city) or use default (Moshi)
        $lat = $request->input('lat');
        $lon = $request->input('lon');
        $city = $request->input('city', 'Moshi');
        
        // Clear cache if refresh is requested
        if ($request->has('refresh')) {
            // Clear current weather cache (last 20 minutes worth)
            for ($i = 0; $i < 20; $i++) {
                $minutes = floor((now()->timestamp - ($i * 600)) / 600) * 600;
                \Illuminate\Support\Facades\Cache::forget('weather_moshi_' . $minutes);
                if ($lat && $lon) {
                    \Illuminate\Support\Facades\Cache::forget('weather_' . round($lat, 2) . '_' . round($lon, 2) . '_' . $minutes);
                }
            }
            // Clear forecast cache (last 2 hours worth)
            for ($i = 0; $i < 4; $i++) {
                $minutes = floor((now()->timestamp - ($i * 1800)) / 1800) * 1800;
                \Illuminate\Support\Facades\Cache::forget('weather_forecast_moshi_' . $minutes);
                if ($lat && $lon) {
                    \Illuminate\Support\Facades\Cache::forget('weather_forecast_' . round($lat, 2) . '_' . round($lon, 2) . '_' . $minutes);
                }
            }
        }
        
        // Get real-time weather data (use provided location or default to Moshi)
        $weather = $this->weatherService->getCurrentWeather($lat, $lon, $city);
        $forecast = $this->weatherService->getForecast($lat, $lon, $city);
        
        // Local attractions data
        $attractions = [
            [
                'name' => 'Mount Kilimanjaro',
                'description' => 'The highest peak in Africa, located just 50km from Moshi',
                'distance' => '50 km',
                'type' => 'Natural',
            ],
            [
                'name' => 'Moshi Town Center',
                'description' => 'Explore local markets, shops, and restaurants',
                'distance' => '2 km',
                'type' => 'Cultural',
            ],
            [
                'name' => 'Materuni Waterfalls',
                'description' => 'Beautiful waterfalls and coffee plantations',
                'distance' => '25 km',
                'type' => 'Natural',
            ],
            [
                'name' => 'Kikuletwa Hot Springs',
                'description' => 'Natural hot springs perfect for relaxation',
                'distance' => '35 km',
                'type' => 'Natural',
            ],
        ];
        
        // Transportation options
        $transportation = [
            [
                'type' => 'Taxi',
                'description' => 'Available 24/7, contact front desk',
                'contact' => '+255 677-155-156',
            ],
            [
                'type' => 'Airport Shuttle',
                'description' => 'Pre-arranged airport pickup service',
                'contact' => 'Book through hotel',
            ],
            [
                'type' => 'Car Rental',
                'description' => 'Self-drive options available',
                'contact' => 'Contact front desk',
            ],
            [
                'type' => 'Local Buses',
                'description' => 'Public transportation to nearby areas',
                'contact' => 'Ask front desk for routes',
            ],
        ];
        
        // Hotel facilities
        $facilities = [
            'WiFi' => 'Free high-speed WiFi throughout the hotel',
            'Restaurant' => 'On-site restaurant serving local and international cuisine',
            'Bar' => 'Fully stocked bar with local and imported beverages',
            'Parking' => 'Secure parking available',
            'Laundry' => 'Laundry service available',
            'Room Service' => '24/7 room service',
        ];
        
        return view('dashboard.customer-local-info', [
            'role' => 'customer',
            'userName' => $user->name ?? 'Guest User',
            'userRole' => 'Customer',
            'user' => $user,
            'weather' => $weather,
            'forecast' => $forecast,
            'weatherService' => $this->weatherService,
            'attractions' => $attractions,
            'transportation' => $transportation,
            'facilities' => $facilities,
        ]);
    }
}

