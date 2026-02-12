@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-info-circle"></i> Room Status Overview</h1>
    <p>View current status of all rooms</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('housekeeper.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Room Status</a></li>
  </ul>
</div>

<!-- Statistics Cards -->
<div class="row mb-3">
  <div class="col-md-3 col-lg-3">
    <div class="widget-small success coloured-icon">
      <i class="icon fa fa-check-circle fa-2x"></i>
      <div class="info">
        <h4>Available</h4>
        <p><b>{{ $statusCounts['available'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-lg-3">
    <div class="widget-small primary coloured-icon">
      <i class="icon fa fa-user fa-2x"></i>
      <div class="info">
        <h4>Occupied</h4>
        <p><b>{{ $statusCounts['occupied'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-lg-3">
    <div class="widget-small warning coloured-icon">
      <i class="icon fa fa-broom fa-2x"></i>
      <div class="info">
        <h4>Needs Cleaning</h4>
        <p><b>{{ $statusCounts['to_be_cleaned'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-lg-3">
    <div class="widget-small danger coloured-icon">
      <i class="icon fa fa-wrench fa-2x"></i>
      <div class="info">
        <h4>Maintenance</h4>
        <p><b>{{ $statusCounts['maintenance'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
</div>

<!-- Rooms Table -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-bed"></i> All Rooms</h3>
      <div class="tile-body">
        <div class="table-responsive">
          <table class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>Room Number</th>
                <th>Room Type</th>
                <th>Status</th>
                <th>Last Cleaned</th>
                <th>Active Issues</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($rooms as $room)
              <tr>
                <td><strong>{{ $room->room_number }}</strong></td>
                <td>{{ $room->room_type }}</td>
                <td>
                  @if($room->status === 'available')
                    <span class="badge badge-success">Available</span>
                  @elseif($room->status === 'occupied')
                    <span class="badge badge-primary">Occupied</span>
                  @elseif($room->status === 'to_be_cleaned')
                    <span class="badge badge-warning">Needs Cleaning</span>
                  @elseif($room->status === 'maintenance')
                    <span class="badge badge-danger">Maintenance</span>
                  @endif
                </td>
                <td>
                  @if($room->latestCleaningLog && $room->latestCleaningLog->cleaned_at)
                    {{ \Carbon\Carbon::parse($room->latestCleaningLog->cleaned_at)->format('M d, Y H:i') }}
                  @else
                    <span class="text-muted">Never</span>
                  @endif
                </td>
                <td>
                  @if($room->issues->count() > 0)
                    <span class="badge badge-danger">{{ $room->issues->count() }} issue(s)</span>
                  @else
                    <span class="badge badge-success">None</span>
                  @endif
                </td>
                <td>
                  @if($room->status === 'to_be_cleaned')
                    <a href="{{ route('housekeeper.rooms.cleaning') }}" class="btn btn-sm btn-success">
                      <i class="fa fa-check"></i> Mark Cleaned
                    </a>
                  @endif
                  @if($room->issues->count() > 0)
                    <a href="{{ route('housekeeper.room-issues') }}?room={{ $room->id }}" class="btn btn-sm btn-info">
                      <i class="fa fa-eye"></i> View Issues
                    </a>
                  @endif
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
