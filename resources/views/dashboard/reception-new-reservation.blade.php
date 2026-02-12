@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-plus-circle"></i> New Reservation</h1>
    <p>Create a new reservation manually</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('reception.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">New Reservation</a></li>
  </ul>
</div>

<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title">Create New Reservation</h3>
      <div class="tile-body">
        <div class="alert alert-info">
          <i class="fa fa-info-circle"></i> <strong>Note:</strong> To create a new reservation, please direct guests to use the online booking system at <a href="{{ route('booking.index') }}" target="_blank">{{ route('booking.index') }}</a>
        </div>
        
        <div class="text-center" style="padding: 50px;">
          <i class="fa fa-calendar-plus-o fa-5x text-muted mb-3"></i>
          <h3>Online Booking System</h3>
          <p class="text-muted">For new reservations, please use the online booking system which handles:</p>
          <ul class="list-unstyled text-left" style="max-width: 500px; margin: 20px auto;">
            <li><i class="fa fa-check text-success"></i> Room availability checking</li>
            <li><i class="fa fa-check text-success"></i> Real-time pricing calculation</li>
            <li><i class="fa fa-check text-success"></i> Payment processing</li>
            <li><i class="fa fa-check text-success"></i> Booking confirmation</li>
            <li><i class="fa fa-check text-success"></i> Email notifications</li>
          </ul>
          <a href="{{ route('booking.index') }}" target="_blank" class="btn btn-primary btn-lg">
            <i class="fa fa-external-link"></i> Open Booking System
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection





