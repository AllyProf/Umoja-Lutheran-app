@extends('dashboard.layouts.app')

@section('content')
@php
    $audiences = [
        'all' => 'All users',
        'reception' => 'Reception',
        'bar_keeper' => 'Counter',
        'cashier' => 'Cashier',
        'accountant' => 'Accountant',
        'manager' => 'Manager',
        'housekeeper' => 'Housekeeper',
        'head_chef' => 'Head Chef',
        'storekeeper' => 'Storekeeper',
        'waiter' => 'Waiter',
    ];
@endphp
<div class="app-title">
    <div>
        <h1><i class="fa fa-bullhorn"></i> Announcements</h1>
        <p>Write a notice. It scrolls under the header, from right to left, for the people you choose.</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="{{ route('super_admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item">Announcements</li>
    </ul>
</div>

<div class="row">
    <div class="col-md-5">
        <div class="tile">
            <h3 class="tile-title">New notice</h3>
            <div class="tile-body">
                <form action="{{ route('super_admin.announcements.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>Message</label>
                        <textarea name="message" class="form-control @error('message') is-invalid @enderror" rows="4" maxlength="500" required placeholder="Example: A new report was added under Reception. Refresh and open Reports.">{{ old('message') }}</textarea>
                        @error('message')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Who should see it</label>
                        <select name="target_role" class="form-control" required>
                            @foreach($audiences as $value => $label)
                                <option value="{{ $value }}" {{ old('target_role', 'all') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Stop showing after (optional)</label>
                        <input type="datetime-local" name="expires_at" class="form-control" value="{{ old('expires_at') }}">
                    </div>
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="send_sms" name="send_sms" value="1" {{ old('send_sms') ? 'checked' : '' }}>
                            <label class="form-check-label" for="send_sms">Also send this notice by SMS to the selected staff</label>
                        </div>
                        <small class="text-muted">Leave this off to show the notice on screen only. All users means every active staff member who has a phone number.</small>
                    </div>
                    <button class="btn btn-primary" type="submit"><i class="fa fa-bullhorn"></i> Publish notice</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-7">
        <div class="tile">
            <h3 class="tile-title">Notices</h3>
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <th>Message</th>
                            <th>Audience</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($announcements as $announcement)
                        <tr>
                            <td>{{ $announcement->message }}</td>
                            <td>{{ $audiences[$announcement->target_role] ?? ucfirst(str_replace('_', ' ', $announcement->target_role)) }}</td>
                            <td>
                                @if($announcement->is_active && (!$announcement->expires_at || $announcement->expires_at->isFuture()))
                                    <span class="badge badge-success">Showing</span>
                                @else
                                    <span class="badge badge-secondary">Hidden</span>
                                @endif
                            </td>
                            <td class="text-nowrap">
                                <form action="{{ route('super_admin.announcements.toggle', $announcement) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm {{ $announcement->is_active ? 'btn-warning' : 'btn-success' }}">
                                        {{ $announcement->is_active ? 'Hide' : 'Show' }}
                                    </button>
                                </form>
                                <form action="{{ route('super_admin.announcements.destroy', $announcement) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this notice?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">No notices yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $announcements->links() }}
        </div>
    </div>
</div>
@endsection
