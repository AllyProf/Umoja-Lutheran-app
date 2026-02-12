@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-exclamation-triangle"></i> Page Not Found</h1>
    <p>404 Error</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="#">404</a></li>
  </ul>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-body text-center" style="padding: 80px 40px;">
        <div style="font-size: 150px; color: #e77a3a; margin-bottom: 30px; font-weight: 700; line-height: 1;">
          404
        </div>
        <h2 style="color: #1a365d; margin-bottom: 20px; font-weight: 600; font-size: 32px;">Page Not Found</h2>
        <p style="font-size: 18px; color: #666; margin-bottom: 40px; max-width: 600px; margin-left: auto; margin-right: auto; line-height: 1.8;">
          Oops! The page you're looking for seems to have checked out. 
          It might have been moved, deleted, or the URL might be incorrect.
        </p>
        <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap; margin-top: 40px;">
          <a href="{{ url('/') }}" class="btn btn-primary" style="padding: 12px 30px; font-size: 16px; border-radius: 50px; font-weight: 600;">
            <i class="fa fa-home"></i> Go to Homepage
          </a>
          @if($userRole ?? null)
            @php
              $dashboardRoute = 'customer.dashboard';
              if ($userRole === 'super_admin') {
                $dashboardRoute = 'super_admin.dashboard';
              } elseif ($userRole === 'manager') {
                $dashboardRoute = 'admin.dashboard';
              } elseif ($userRole === 'reception') {
                $dashboardRoute = 'reception.dashboard';
              }
            @endphp
            <a href="{{ route($dashboardRoute) }}" class="btn btn-secondary" style="padding: 12px 30px; font-size: 16px; border-radius: 50px; background: #1a365d; color: white; border: none; font-weight: 600;">
              <i class="fa fa-dashboard"></i> Go to Dashboard
            </a>
          @endif
          <a href="javascript:history.back()" class="btn btn-secondary" style="padding: 12px 30px; font-size: 16px; border-radius: 50px; background: #6c757d; color: white; border: none; font-weight: 600;">
            <i class="fa fa-arrow-left"></i> Go Back
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
