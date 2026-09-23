@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-user"></i> {{ $user ? __('Edit User') : __('Create New User') }}</h1>
    <p>{{ $user ? __('Update user information') : __('Add a new user to the system') }}</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('super_admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('super_admin.users') }}">{{ __('Users') }}</a></li>
    <li class="breadcrumb-item"><a href="#">{{ $user ? __('Edit') : __('Create') }}</a></li>
  </ul>
</div>

<div class="row">
  <div class="col-md-8">
    <div class="tile">
      <h3 class="tile-title">{{ $user ? __('Edit User') : __('Create New User') }}</h3>
      <div class="tile-body">
        <form action="{{ $user ? route('super_admin.users.update', $user->id) : route('super_admin.users.store') }}"
              method="POST">
          @csrf
          @if($user)
            @method('PUT')
          @endif

          <div class="form-group">
            <label for="name">{{ __('Full Name') }} <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name"
                   class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $user->name ?? '') }}" required>
            @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="form-group">
            <label for="email">{{ __('Email Address') }} <span class="text-danger">*</span></label>
            <input type="email" name="email" id="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email', $user->email ?? '') }}" required>
            @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="form-group">
            <label for="phone">{{ __('Phone Number') }} <span class="text-danger">*</span></label>
            <input type="text" name="phone" id="phone"
                   class="form-control @error('phone') is-invalid @enderror"
                   value="{{ old('phone', $user->phone ?? '+255') }}"
                   placeholder="+255 7XX XXX XXX" required>
            @error('phone')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">{{ __('For staff, credentials will be sent to this number via SMS.') }}</small>
          </div>

          <div class="form-group">
            <label for="role">{{ __('Role') }} <span class="text-danger">*</span></label>
            <select name="role" id="role"
                    class="form-control @error('role') is-invalid @enderror" required>
              <option value="">{{ __('Select Role') }}</option>
              <option value="super_admin" {{ old('role', $user->role ?? '') == 'super_admin' ? 'selected' : '' }}>
                {{ __('Super Administrator') }}
              </option>
              <option value="manager" {{ old('role', $user->role ?? '') == 'manager' ? 'selected' : '' }}>
                {{ __('Manager') }}
              </option>
              <option value="reception" {{ old('role', $user->role ?? '') == 'reception' ? 'selected' : '' }}>
                {{ __('Reception Staff') }}
              </option>
              <option value="bar_keeper" {{ old('role', $user->role ?? '') == 'bar_keeper' ? 'selected' : '' }}>
                {{ __('Counter') }}
              </option>
              <option value="head_chef" {{ old('role', $user->role ?? '') == 'head_chef' ? 'selected' : '' }}>
                {{ __('Head Chef') }}
              </option>
              <option value="housekeeper" {{ old('role', $user->role ?? '') == 'housekeeper' ? 'selected' : '' }}>
                {{ __('Housekeeper') }}
              </option>
              <option value="waiter" {{ old('role', $user->role ?? '') == 'waiter' ? 'selected' : '' }}>
                {{ __('Waiter') }}
              </option>
              <option value="storekeeper" {{ old('role', $user->role ?? '') == 'storekeeper' ? 'selected' : '' }}>
                {{ __('Storekeeper') }}
              </option>
              <option value="accountant" {{ old('role', $user->role ?? '') == 'accountant' ? 'selected' : '' }}>
                {{ __('Accountant') }}
              </option>
              <option value="cashier" {{ old('role', $user->role ?? '') == 'cashier' ? 'selected' : '' }}>
                {{ __('Cashier') }}
              </option>
              <option value="guest" {{ old('role', $user->role ?? '') == 'guest' ? 'selected' : '' }}>
                {{ __('Guest') }}
              </option>
            </select>
            @error('role')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          @if($user)
          <div class="form-group">
            <label for="password">{{ __('New Password (leave blank to keep current)') }}</label>
            <div class="input-group">
              <input type="password" name="password" id="password"
                     class="form-control @error('password') is-invalid @enderror"
                     minlength="8">
              <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="button" id="togglePassword"
                        style="border-top-right-radius: 0.25rem; border-bottom-right-radius: 0.25rem;">
                  <i class="fa fa-eye" id="togglePasswordIcon"></i>
                </button>
              </div>
            </div>
            @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">{{ __('Minimum 8 characters') }}</small>
          </div>

          <div class="form-group">
            <label for="password_confirmation">{{ __('Confirm New Password (required if changing password)') }}</label>
            <div class="input-group">
              <input type="password" name="password_confirmation" id="password_confirmation"
                     class="form-control @error('password_confirmation') is-invalid @enderror"
                     minlength="8">
              <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirmation"
                        style="border-top-right-radius: 0.25rem; border-bottom-right-radius: 0.25rem;">
                  <i class="fa fa-eye" id="togglePasswordConfirmationIcon"></i>
                </button>
              </div>
            </div>
            @error('password_confirmation')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          @else
          <div class="alert alert-info">
            <i class="fa fa-info-circle"></i>
            <strong>{{ __('Password Information:') }}</strong>
            {{ __('For staff accounts, password will be auto-generated from the first name (in uppercase).') }}
            {{ __('A welcome email with login credentials will be sent to the user\'s email address.') }}
            @if(!in_array(old('role', ''), ['super_admin', 'manager', 'reception']))
            <br><br>{{ __('For guest accounts, a password will be required during account setup.') }}
            @endif
          </div>
          @endif

          <div class="form-group">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                     value="1" {{ old('is_active', $user->is_active ?? true) ? 'checked' : '' }}>
              <label class="form-check-label" for="is_active">
                {{ __('Active Account') }}
              </label>
            </div>
          </div>

          <div class="tile-footer">
            <button type="submit" class="btn btn-primary">
              <i class="fa fa-save"></i> {{ $user ? __('Update User') : __('Create User') }}
            </button>
            <a href="{{ route('super_admin.users') }}" class="btn btn-secondary">
              <i class="fa fa-times"></i> {{ __('Cancel') }}
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const passwordConfirmationInput = document.getElementById('password_confirmation');
    const roleSelect = document.getElementById('role');
    const togglePasswordBtn = document.getElementById('togglePassword');
    const togglePasswordIcon = document.getElementById('togglePasswordIcon');
    const togglePasswordConfirmationBtn = document.getElementById('togglePasswordConfirmation');
    const togglePasswordConfirmationIcon = document.getElementById('togglePasswordConfirmationIcon');

    function updatePasswordRequirements() {
        const selectedRole = roleSelect.value;
        const isStaff = ['super_admin', 'manager', 'reception', 'bar_keeper', 'head_chef', 'housekeeper', 'waiter', 'storekeeper', 'accountant', 'cashier'].includes(selectedRole);
        const isCreating = @json(!$user);

        if (isStaff && isCreating) {
            if (passwordInput) passwordInput.removeAttribute('required');
            if (passwordConfirmationInput) passwordConfirmationInput.removeAttribute('required');
        } else {
            if (isCreating) {
                if (passwordInput) passwordInput.setAttribute('required', 'required');
                if (passwordConfirmationInput) passwordConfirmationInput.setAttribute('required', 'required');
            }
        }
    }

    if (roleSelect) {
        roleSelect.addEventListener('change', updatePasswordRequirements);
        updatePasswordRequirements();
    }

    if (togglePasswordBtn) {
        togglePasswordBtn.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            togglePasswordIcon.classList.toggle('fa-eye');
            togglePasswordIcon.classList.toggle('fa-eye-slash');
        });
    }

    if (togglePasswordConfirmationBtn && passwordConfirmationInput) {
        togglePasswordConfirmationBtn.addEventListener('click', function() {
            const type = passwordConfirmationInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordConfirmationInput.setAttribute('type', type);
            togglePasswordConfirmationIcon.classList.toggle('fa-eye');
            togglePasswordConfirmationIcon.classList.toggle('fa-eye-slash');
        });
    }
});
</script>
@endsection
