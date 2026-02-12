@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-headphones"></i> Support</h1>
    <p>Contact us for assistance</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Support</a></li>
  </ul>
</div>

<div class="row">
  <div class="col-md-8">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-headphones"></i> Contact Support</h3>
      <div class="tile-body">
        <p class="mb-4">We're here to help! If you have any questions, concerns, or need assistance with your booking, please don't hesitate to contact us using any of the methods below.</p>
        
        <!-- Mobile/WhatsApp Section -->
        <div class="mb-4" style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; border-left: 4px solid #25D366;">
          <h4 style="color: #333; margin-bottom: 15px;">
            <i class="fa fa-mobile" style="color: #25D366;"></i> Mobile/WhatsApp
          </h4>
          <div class="row">
            <div class="col-md-6 mb-3">
              <div style="display: flex; align-items: center; gap: 10px;">
                <i class="fa fa-phone" style="color: #940000; font-size: 18px;"></i>
                <div>
                  <strong style="display: block; margin-bottom: 5px;">Phone/WhatsApp</strong>
                  <a href="tel:+255677155156" style="color: #940000; text-decoration: none; font-size: 16px; font-weight: 600;">
                    0677-155-156
                  </a>
                </div>
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <div style="display: flex; align-items: center; gap: 10px;">
                <i class="fa fa-phone" style="color: #940000; font-size: 18px;"></i>
                <div>
                  <strong style="display: block; margin-bottom: 5px;">International</strong>
                  <a href="tel:+255677155157" style="color: #940000; text-decoration: none; font-size: 16px; font-weight: 600;">
                    +255 677-155-157
                  </a>
                </div>
              </div>
            </div>
          </div>
          <p class="text-muted mb-0" style="font-size: 13px; margin-top: 10px;">
            <i class="fa fa-info-circle"></i> Available 24/7 for your convenience
          </p>
        </div>
        
        <!-- Email Section -->
        <div class="mb-4" style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; border-left: 4px solid #007bff;">
          <h4 style="color: #333; margin-bottom: 15px;">
            <i class="fa fa-envelope" style="color: #007bff;"></i> Email
          </h4>
          <div class="row">
            <div class="col-md-6 mb-3">
              <div style="display: flex; align-items: flex-start; gap: 10px;">
                <i class="fa fa-envelope-o" style="color: #940000; font-size: 18px; margin-top: 3px;"></i>
                <div>
                  <strong style="display: block; margin-bottom: 5px;">Primary Email</strong>
                  <a href="mailto:info@Umoj Lutheran Hostelhotel.co.tz" style="color: #940000; text-decoration: none; font-size: 14px; word-break: break-all;">
                    info@Umoj Lutheran Hostelhotel.co.tz
                  </a>
                </div>
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <div style="display: flex; align-items: flex-start; gap: 10px;">
                <i class="fa fa-envelope-o" style="color: #940000; font-size: 18px; margin-top: 3px;"></i>
                <div>
                  <strong style="display: block; margin-bottom: 5px;">Alternative Email</strong>
                  <a href="mailto:infoUmoj Lutheran Hostelhotel@gmail.com" style="color: #940000; text-decoration: none; font-size: 14px; word-break: break-all;">
                    infoUmoj Lutheran Hostelhotel@gmail.com
                  </a>
                </div>
              </div>
            </div>
          </div>
          <p class="text-muted mb-0" style="font-size: 13px; margin-top: 10px;">
            <i class="fa fa-info-circle"></i> We typically respond within 24 hours
          </p>
        </div>
        
        <!-- Quick Actions -->
        <div class="mt-4">
          <h5 style="color: #940000; margin-bottom: 15px;">
            <i class="fa fa-bolt"></i> Quick Actions
          </h5>
          <div class="row">
            <div class="col-md-6 mb-3">
              <a href="{{ route('customer.issues.index') }}" class="btn btn-warning btn-block" style="padding: 12px;">
                <i class="fa fa-exclamation-triangle"></i> Report an Issue
              </a>
            </div>
            <div class="col-md-6 mb-3">
              <a href="{{ route('customer.dashboard') }}" class="btn btn-info btn-block" style="padding: 12px;">
                <i class="fa fa-dashboard"></i> Go to Dashboard
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-md-4">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-info-circle"></i> Need Help?</h3>
      <div class="tile-body">
        <p style="font-size: 14px; line-height: 1.6;">
          Our support team is available around the clock to assist you with:
        </p>
        <ul style="font-size: 13px; line-height: 2;">
          <li>Booking inquiries</li>
          <li>Room information</li>
          <li>Payment assistance</li>
          <li>Technical support</li>
          <li>General questions</li>
        </ul>
        <div class="alert alert-info mt-3" style="font-size: 12px;">
          <i class="fa fa-clock-o"></i> <strong>Response Time:</strong><br>
          Phone/WhatsApp: Immediate<br>
          Email: Within 24 hours
        </div>
      </div>
    </div>
    
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-map-marker"></i> Visit Us</h3>
      <div class="tile-body">
        <p style="font-size: 14px; margin-bottom: 10px;">
          <strong>Umoj Lutheran Hostel</strong>
        </p>
        <p style="font-size: 13px; color: #666; margin: 0;">
          Feel free to visit our reception desk for in-person assistance during your stay.
        </p>
      </div>
    </div>
  </div>
</div>

@endsection

@section('styles')
<style>
  /* Mobile Responsive Styles */
  @media (max-width: 768px) {
    .col-md-8,
    .col-md-4 {
      flex: 0 0 100%;
      max-width: 100%;
      margin-bottom: 20px;
    }
    
    .col-md-6 {
      flex: 0 0 100%;
      max-width: 100%;
      margin-bottom: 15px;
    }
    
    .tile-title {
      font-size: 18px;
    }
    
    .btn-block {
      width: 100%;
      font-size: 14px;
      padding: 12px 20px;
    }
    
    .mb-4 {
      padding: 15px !important;
    }
    
    .mb-4 h4 {
      font-size: 16px;
    }
    
    .mb-4 a {
      font-size: 14px !important;
    }
    
    ul {
      padding-left: 20px;
    }
  }
</style>
@endsection








