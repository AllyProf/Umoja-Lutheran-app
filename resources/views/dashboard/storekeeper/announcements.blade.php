@extends('dashboard.layouts.app')

@section('content')
    <div class="app-title">
        <div>
            <h1><i class="fa fa-bullhorn"></i> Broadcast Alerts</h1>
            <p>Send scrolling messages to Chef and Counter staff dashboards</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="#">Broadcast Alerts</a></li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-5">
            <div class="tile">
                <h3 class="tile-title">Create New Broadcast</h3>
                <div class="tile-body">
                    <form action="{{ route('storekeeper.announcements.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="control-label">Message</label>
                            <textarea name="message" class="form-control @error('message') is-invalid @enderror" rows="4"
                                placeholder="Enter the message to display (e.g., Soda stock is low, please prioritize water)..."
                                required>{{ old('message') }}</textarea>
                            @error('message')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="control-label">Target Staff</label>
                            <select name="target_role" class="form-control @error('target_role') is-invalid @enderror"
                                required>
                                <option value="both" {{ old('target_role') == 'both' ? 'selected' : '' }}>Both Chef & Counter
                                </option>
                                <option value="head_chef" {{ old('target_role') == 'head_chef' ? 'selected' : '' }}>Chef
                                    (Kitchen) Only</option>
                                <option value="bar_keeper" {{ old('target_role') == 'bar_keeper' ? 'selected' : '' }}>Counter
                                    (Bar) Only</option>
                                <option value="housekeeper" {{ old('target_role') == 'housekeeper' ? 'selected' : '' }}>
                                    Housekeeper Only</option>
                                <option value="all" {{ old('target_role') == 'all' ? 'selected' : '' }}>All Staff</option>
                            </select>
                            @error('target_role')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="control-label">Expiraration Date (Optional)</label>
                            <input type="datetime-local" name="expires_at"
                                class="form-control @error('expires_at') is-invalid @enderror"
                                value="{{ old('expires_at') }}">
                            <small class="text-muted">Broadcast will automatically stop after this time.</small>
                            @error('expires_at')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="tile-footer">
                            <button class="btn btn-primary" type="submit"><i
                                    class="fa fa-fw fa-lg fa-paper-plane"></i>Broadcast Now</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="tile">
                <h3 class="tile-title">Active & Recent Broadcasts</h3>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>Message</th>
                                <th>Targets</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($announcements as $announcement)
                                <tr>
                                    <td>{{ $announcement->message }}</td>
                                    <td>
                                        @if($announcement->target_role == 'both')
                                            <span class="badge badge-info">Chef & Counter</span>
                                        @elseif($announcement->target_role == 'head_chef')
                                            <span class="badge badge-primary">Chef Only</span>
                                        @elseif($announcement->target_role == 'bar_keeper')
                                            <span class="badge badge-secondary">Counter Only</span>
                                        @elseif($announcement->target_role == 'housekeeper')
                                            <span class="badge badge-dark">Housekeeper Only</span>
                                        @else
                                            <span class="badge badge-warning">All Staff</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($announcement->is_active && (!$announcement->expires_at || $announcement->expires_at->isFuture()))
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <form action="{{ route('storekeeper.announcements.toggle', $announcement) }}"
                                                method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit"
                                                    class="btn btn-sm {{ $announcement->is_active ? 'btn-warning' : 'btn-success' }}"
                                                    title="{{ $announcement->is_active ? 'Deactivate' : 'Activate' }}">
                                                    <i class="fa {{ $announcement->is_active ? 'fa-pause' : 'fa-play' }}"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('storekeeper.announcements.destroy', $announcement) }}"
                                                method="POST" style="display:inline;"
                                                onsubmit="return confirm('Delete this broadcast?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">No broadcasts found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $announcements->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
