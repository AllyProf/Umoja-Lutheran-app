@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-map-marker"></i> Local Information</h1>
    <p>Discover Moshi and surrounding areas</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="#">Local Info</a></li>
  </ul>
</div>

<!-- Weather Widget -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-title-w-btn">
        <h3 class="tile-title">
          <i class="fa fa-cloud"></i> 
          <span id="weatherLocationTitle">
            @if($weather && is_array($weather) && isset($weather['city']))
              Weather in {{ $weather['city'] }}{{ isset($weather['country']) ? ', ' . $weather['country'] : '' }}
            @else
              Weather in Moshi
            @endif
          </span>
        </h3>
        <div class="btn-group">
          <button class="btn btn-sm btn-info" onclick="getUserLocation()" id="getLocationBtn" title="Get weather for your current location">
            <i class="fa fa-map-marker"></i> My Location
          </button>
          <button class="btn btn-sm btn-secondary" onclick="resetToMoshi()" id="resetLocationBtn" title="Reset to Moshi, Tanzania" style="display: none;">
            <i class="fa fa-home"></i> Moshi
          </button>
          <button class="btn btn-sm btn-primary" onclick="refreshWeather()" id="refreshWeatherBtn">
            <i class="fa fa-refresh"></i> Refresh
          </button>
        </div>
      </div>
      <div class="tile-body">
        @if($weather)
        <div class="row">
          <!-- Current Weather -->
          <div class="col-md-6">
            <div class="text-center" style="padding: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px; color: white;">
              <i class="fa {{ $weatherService->getWeatherIconClass($weather['icon']) }} fa-5x mb-3" style="color: #ffd700;"></i>
              <h2 style="color: white; margin: 0;">{{ $weather['temperature'] }}°C</h2>
              <p style="color: rgba(255,255,255,0.9); margin: 5px 0;">{{ $weather['description'] }}</p>
              <p style="color: rgba(255,255,255,0.8); font-size: 14px; margin: 0;">
                Feels like {{ $weather['feels_like'] }}°C
              </p>
              <hr style="border-color: rgba(255,255,255,0.3); margin: 15px 0;">
              <div class="row text-center">
                <div class="col-6">
                  <i class="fa fa-arrow-up"></i><br>
                  <small>{{ $weather['temp_max'] }}°C</small>
                </div>
                <div class="col-6">
                  <i class="fa fa-arrow-down"></i><br>
                  <small>{{ $weather['temp_min'] }}°C</small>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Weather Details -->
          <div class="col-md-6">
            <h5 style="color: #940000; margin-bottom: 15px;">Weather Details</h5>
            <table class="table table-sm table-borderless">
              <tr>
                <td style="width: 40px;"><i class="fa fa-map-marker" style="color: #940000;"></i></td>
                <td><strong>Location:</strong></td>
                <td>{{ $weather['city'] }}, {{ $weather['country'] }}</td>
              </tr>
              <tr>
                <td><i class="fa fa-tint" style="color: #940000;"></i></td>
                <td><strong>Humidity:</strong></td>
                <td>{{ $weather['humidity'] }}%</td>
              </tr>
              <tr>
                <td><i class="fa fa-compress" style="color: #940000;"></i></td>
                <td><strong>Pressure:</strong></td>
                <td>{{ $weather['pressure'] }} hPa</td>
              </tr>
              <tr>
                <td><i class="fa fa-leaf" style="color: #940000;"></i></td>
                <td><strong>Wind:</strong></td>
                <td>{{ $weather['wind_speed'] }} km/h {{ $weather['wind_direction'] }}</td>
              </tr>
              @if($weather['visibility'])
              <tr>
                <td><i class="fa fa-eye" style="color: #940000;"></i></td>
                <td><strong>Visibility:</strong></td>
                <td>{{ $weather['visibility'] }} km</td>
              </tr>
              @endif
              @if($weather['sunrise'])
              <tr>
                <td><i class="fa fa-sun-o" style="color: #ffd700;"></i></td>
                <td><strong>Sunrise:</strong></td>
                <td>{{ $weather['sunrise'] }}</td>
              </tr>
              @endif
              @if($weather['sunset'])
              <tr>
                <td><i class="fa fa-moon-o" style="color: #4a5568;"></i></td>
                <td><strong>Sunset:</strong></td>
                <td>{{ $weather['sunset'] }}</td>
              </tr>
              @endif
              <tr>
                <td><i class="fa fa-clock-o" style="color: #940000;"></i></td>
                <td><strong>Updated:</strong></td>
                <td>{{ $weather['updated_at'] }}</td>
              </tr>
            </table>
          </div>
        </div>
        
        <!-- Forecast -->
        @if($forecast && count($forecast) > 0)
        <hr style="margin: 20px 0;">
        <h5 style="color: #940000; margin-bottom: 15px;">5-Day Forecast</h5>
        <div class="row">
          @foreach(array_slice($forecast, 0, 5) as $day)
          <div class="col-md-2 col-sm-4 col-6 mb-3">
            <div class="text-center" style="padding: 15px; background: #f8f9fa; border-radius: 8px;">
              <p style="margin: 0; font-weight: bold; color: #940000;">{{ date('D', strtotime($day['date'])) }}</p>
              <p style="margin: 5px 0; font-size: 12px; color: #666;">{{ date('M d', strtotime($day['date'])) }}</p>
              <i class="fa {{ $weatherService->getWeatherIconClass($day['icon']) }} fa-2x mb-2" style="color: #667eea;"></i>
              <p style="margin: 5px 0; font-size: 18px; font-weight: bold;">{{ $day['temperature'] }}°C</p>
              <p style="margin: 0; font-size: 11px; color: #666;">
                <i class="fa fa-arrow-up"></i> {{ $day['temp_max'] }}° 
                <i class="fa fa-arrow-down"></i> {{ $day['temp_min'] }}°
              </p>
              <p style="margin: 5px 0 0 0; font-size: 11px; color: #999;">{{ $day['description'] }}</p>
            </div>
          </div>
          @endforeach
        </div>
        @endif
        @else
        <div class="text-center" style="padding: 30px;">
          <i class="fa fa-cloud fa-4x text-muted mb-3"></i>
          <h4>Moshi, Tanzania</h4>
          <p class="text-muted">Unable to fetch real-time weather data.</p>
          <p><strong>Average Temperature:</strong> 20-25°C (68-77°F)</p>
          <p><strong>Best Time to Visit:</strong> June to October (Dry season)</p>
          <small class="text-muted">Please check your weather app or ask the front desk for current conditions.</small>
          
          @if(empty(env('OPENWEATHER_API_KEY')))
          <div class="alert alert-warning mt-3" style="text-align: left; max-width: 600px; margin: 20px auto;">
            <strong><i class="fa fa-info-circle"></i> Note:</strong> Weather API is not configured. 
            To enable real-time weather, add your OpenWeatherMap API key to the <code>.env</code> file:
            <br><code>OPENWEATHER_API_KEY=your_api_key_here</code>
            <br><small>Get a free API key at <a href="https://openweathermap.org/api" target="_blank">openweathermap.org</a></small>
          </div>
          @elseif(is_array($weather) && isset($weather['error']) && $weather['error'] === 'invalid_api_key')
          <div class="alert alert-danger mt-3" style="text-align: left; max-width: 600px; margin: 20px auto;">
            <strong><i class="fa fa-exclamation-triangle"></i> API Key Error:</strong>
            <p>The OpenWeatherMap API key in your <code>.env</code> file is invalid or not activated.</p>
            <p><strong>To fix this:</strong></p>
            <ol style="text-align: left; margin: 10px 0;">
              <li>Log in to <a href="https://home.openweathermap.org/api_keys" target="_blank">OpenWeatherMap API Keys</a></li>
              <li>Check if your API key status is "Active"</li>
              <li>If it's not active, wait up to 2 hours for activation (new keys need time to activate)</li>
              <li>If the key is invalid, generate a new one and update your <code>.env</code> file</li>
              <li>After updating, run: <code>php artisan config:clear</code> and <code>php artisan cache:clear</code></li>
            </ol>
            <p><small>Current API Key: <code>{{ substr(env('OPENWEATHER_API_KEY'), 0, 8) }}...</code></small></p>
          </div>
          @endif
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Local Attractions -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-map"></i> Local Attractions</h3>
      <div class="tile-body">
        <div class="row">
          @foreach($attractions as $attraction)
          <div class="col-md-6 mb-3">
            <div class="card" style="border-left: 4px solid #940000;">
              <div class="card-body">
                <h5 class="card-title">
                  <i class="fa fa-{{ $attraction['type'] === 'Natural' ? 'tree' : 'building' }}" style="color: #940000;"></i>
                  {{ $attraction['name'] }}
                </h5>
                <p class="card-text">{{ $attraction['description'] }}</p>
                <p class="card-text">
                  <small class="text-muted">
                    <i class="fa fa-map-marker"></i> Distance: {{ $attraction['distance'] }}
                    <span class="badge badge-secondary ml-2">{{ $attraction['type'] }}</span>
                  </small>
                </p>
              </div>
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Transportation Options -->
<div class="row mb-3">
  <div class="col-md-6">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-car"></i> Transportation</h3>
      <div class="tile-body">
        <div class="list-group">
          @foreach($transportation as $transport)
          <div class="list-group-item">
            <h6 class="mb-1">
              <i class="fa fa-{{ $transport['type'] === 'Taxi' ? 'taxi' : ($transport['type'] === 'Airport Shuttle' ? 'plane' : ($transport['type'] === 'Car Rental' ? 'car' : 'bus')) }}" style="color: #940000;"></i>
              {{ $transport['type'] }}
            </h6>
            <p class="mb-1">{{ $transport['description'] }}</p>
            <small class="text-muted">
              <i class="fa fa-phone"></i> {{ $transport['contact'] }}
            </small>
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
  
  <!-- Hotel Facilities -->
  <div class="col-md-6">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-hotel"></i> Hotel Facilities</h3>
      <div class="tile-body">
        <ul class="list-unstyled">
          @foreach($facilities as $facility => $description)
          <li class="mb-3">
            <h6>
              <i class="fa fa-check-circle" style="color: #28a745;"></i>
              <strong>{{ $facility }}</strong>
            </h6>
            <p class="text-muted mb-0">{{ $description }}</p>
          </li>
          @endforeach
        </ul>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
let currentLat = null;
let currentLon = null;
let currentCity = 'Moshi';

// Get user's current location
function getUserLocation() {
    const btn = document.getElementById('getLocationBtn');
    const originalHtml = btn.innerHTML;
    
    if (!navigator.geolocation) {
        alert('Geolocation is not supported by your browser.');
        return;
    }
    
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Getting location...';
    
    navigator.geolocation.getCurrentPosition(
        function(position) {
            currentLat = position.coords.latitude;
            currentLon = position.coords.longitude;
            
            // Update location and fetch weather
            updateWeatherForLocation(currentLat, currentLon);
            
            btn.disabled = false;
            btn.innerHTML = originalHtml;
            
            // Show reset button
            document.getElementById('resetLocationBtn').style.display = 'inline-block';
        },
        function(error) {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
            
            let errorMsg = 'Unable to get your location. ';
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    errorMsg += 'Please allow location access in your browser settings.';
                    break;
                case error.POSITION_UNAVAILABLE:
                    errorMsg += 'Location information is unavailable.';
                    break;
                case error.TIMEOUT:
                    errorMsg += 'Location request timed out.';
                    break;
                default:
                    errorMsg += 'An unknown error occurred.';
                    break;
            }
            alert(errorMsg);
        },
        {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        }
    );
}

// Update weather for specific location
function updateWeatherForLocation(lat, lon, city = null) {
    const refreshBtn = document.getElementById('refreshWeatherBtn');
    const originalHtml = refreshBtn.innerHTML;
    
    refreshBtn.disabled = true;
    refreshBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Loading...';
    
    // Build URL with location parameters
    const url = new URL(window.location.href);
    url.searchParams.set('lat', lat);
    url.searchParams.set('lon', lon);
    if (city) {
        url.searchParams.set('city', city);
    }
    url.searchParams.set('refresh', new Date().getTime());
    
    // Reload page with new location
    window.location.href = url.toString();
}

// Reset to Moshi location
function resetToMoshi() {
    const url = new URL(window.location.href);
    url.searchParams.delete('lat');
    url.searchParams.delete('lon');
    url.searchParams.set('city', 'Moshi');
    url.searchParams.set('refresh', new Date().getTime());
    
    window.location.href = url.toString();
}

// Update location title based on current location
function updateLocationTitle() {
    const urlParams = new URLSearchParams(window.location.search);
    const lat = urlParams.get('lat');
    const lon = urlParams.get('lon');
    const city = urlParams.get('city') || 'Moshi';
    
    // Get the actual city name from the weather data if available
    const weatherData = @json($weather ?? null);
    
    if (lat && lon) {
        // If we have weather data with city name, use it; otherwise show "Your Location"
        if (weatherData && weatherData.city) {
            const country = weatherData.country ? ', ' + weatherData.country : '';
            document.getElementById('weatherLocationTitle').textContent = 'Weather in ' + weatherData.city + country;
        } else {
            document.getElementById('weatherLocationTitle').textContent = 'Weather at Your Location';
        }
        document.getElementById('resetLocationBtn').style.display = 'inline-block';
        currentLat = parseFloat(lat);
        currentLon = parseFloat(lon);
    } else {
        // Use the city from weather data or fallback to URL parameter
        if (weatherData && weatherData.city) {
            const country = weatherData.country ? ', ' + weatherData.country : '';
            document.getElementById('weatherLocationTitle').textContent = 'Weather in ' + weatherData.city + country;
        } else {
            document.getElementById('weatherLocationTitle').textContent = 'Weather in ' + city;
        }
        document.getElementById('resetLocationBtn').style.display = 'none';
        currentCity = city;
    }
}

function refreshWeather() {
    const btn = document.getElementById('refreshWeatherBtn');
    const originalHtml = btn.innerHTML;
    
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Refreshing...';
    
    // Build URL with current location parameters
    const url = new URL(window.location.href);
    url.searchParams.set('refresh', new Date().getTime());
    
    // Reload the page to show updated weather
    window.location.href = url.toString();
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateLocationTitle();
    
    // Auto-refresh weather every 10 minutes (only if not using custom location)
    const urlParams = new URLSearchParams(window.location.search);
    if (!urlParams.get('lat') && !urlParams.get('lon')) {
        setInterval(function() {
            // Silently refresh in background
            const url = new URL(window.location.href);
            url.searchParams.set('refresh', new Date().getTime());
            fetch(url.toString(), {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                }
            });
        }, 600000); // 10 minutes
    }
});
</script>
@endsection

