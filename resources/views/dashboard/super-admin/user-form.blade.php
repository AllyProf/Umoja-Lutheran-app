@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-user"></i> {{ $user ? 'Edit User' : 'Create New User' }}</h1>
    <p>{{ $user ? 'Update user information' : 'Add a new user to the system' }}</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('super_admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('super_admin.users') }}">Users</a></li>
    <li class="breadcrumb-item"><a href="#">{{ $user ? 'Edit' : 'Create' }}</a></li>
  </ul>
</div>

<div class="row">
  <div class="col-md-8">
    <div class="tile">
      <h3 class="tile-title">{{ $user ? 'Edit User' : 'Create New User' }}</h3>
      <div class="tile-body">
        <form action="{{ $user ? route('super_admin.users.update', $user->id) : route('super_admin.users.store') }}" 
              method="POST">
          @csrf
          @if($user)
            @method('PUT')
          @endif
          
          <div class="form-group">
            <label for="name">Full Name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" 
                   class="form-control @error('name') is-invalid @enderror" 
                   value="{{ old('name', $user->name ?? '') }}" required>
            @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <label for="email">Email Address <span class="text-danger">*</span></label>
            <input type="email" name="email" id="email" 
                   class="form-control @error('email') is-invalid @enderror" 
                   value="{{ old('email', $user->email ?? '') }}" required>
            @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <label for="role">Role <span class="text-danger">*</span></label>
            <select name="role" id="role" 
                    class="form-control @error('role') is-invalid @enderror" required>
              <option value="">Select Role</option>
              <option value="super_admin" {{ old('role', $user->role ?? '') == 'super_admin' ? 'selected' : '' }}>
                Super Administrator
              </option>
              <option value="manager" {{ old('role', $user->role ?? '') == 'manager' ? 'selected' : '' }}>
                Manager
              </option>
              <option value="reception" {{ old('role', $user->role ?? '') == 'reception' ? 'selected' : '' }}>
                Reception Staff
              </option>
              <option value="bar_keeper" {{ old('role', $user->role ?? '') == 'bar_keeper' ? 'selected' : '' }}>
                Bar Keeper
              </option>
              <option value="head_chef" {{ old('role', $user->role ?? '') == 'head_chef' ? 'selected' : '' }}>
                Head Chef
              </option>
              <option value="housekeeper" {{ old('role', $user->role ?? '') == 'housekeeper' ? 'selected' : '' }}>
                Housekeeper
              </option>
              <option value="waiter" {{ old('role', $user->role ?? '') == 'waiter' ? 'selected' : '' }}>
                Waiter
              </option>
              <option value="guest" {{ old('role', $user->role ?? '') == 'guest' ? 'selected' : '' }}>
                Guest
              </option>
            </select>
            @error('role')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          @if($user)
          <!-- Password fields only shown when editing -->
          <div class="form-group">
            <label for="password">New Password (leave blank to keep current)</label>
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
            <small class="form-text text-muted">Minimum 8 characters</small>
          </div>
          
          <div class="form-group">
            <label for="password_confirmation">Confirm New Password (required if changing password)</label>
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
          <!-- Info message for new accounts -->
          <div class="alert alert-info">
            <i class="fa fa-info-circle"></i> 
            <strong>Password Information:</strong> 
            For staff accounts, password will be auto-generated from the first name (in uppercase). 
            A welcome email with login credentials will be sent to the user's email address.
            @if(!in_array(old('role', ''), ['super_admin', 'manager', 'reception']))
            <br><br>For guest accounts, a password will be required during account setup.
            @endif
          </div>
          @endif
          
          <div class="form-group">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="is_active" id="is_active" 
                     value="1" {{ old('is_active', $user->is_active ?? true) ? 'checked' : '' }}>
              <label class="form-check-label" for="is_active">
                Active Account
              </label>
            </div>
          </div>
          
          <div class="tile-footer">
            <button type="submit" class="btn btn-primary">
              <i class="fa fa-save"></i> {{ $user ? 'Update User' : 'Create User' }}
            </button>
            <a href="{{ route('super_admin.users') }}" class="btn btn-secondary">
              <i class="fa fa-times"></i> Cancel
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
    const passwordStrengthDiv = document.getElementById('passwordStrength');
    const passwordStrengthBar = document.getElementById('passwordStrengthBar');
    const passwordStrengthText = document.getElementById('passwordStrengthText');
    const passwordMatchText = document.getElementById('passwordMatchText');
    const passwordRequired = document.getElementById('passwordRequired');
    const passwordConfirmationRequired = document.getElementById('passwordConfirmationRequired');
    const passwordHelpText = document.getElementById('passwordHelpText');
    const staffPasswordInfo = document.getElementById('staffPasswordInfo');
    
    // Function to update password field requirements based on role
    function updatePasswordRequirements() {
        const selectedRole = roleSelect.value;
        const isStaff = ['super_admin', 'manager', 'reception', 'bar_keeper', 'head_chef', 'housekeeper', 'waiter'].includes(selectedRole);
        const isCreating = @json(!$user);
        
        if (isStaff && isCreating) {
            // Staff accounts: password is optional
            if (passwordInput) passwordInput.removeAttribute('required');
            if (passwordConfirmationInput) passwordConfirmationInput.removeAttribute('required');
            if (passwordRequired) passwordRequired.style.display = 'none';
            if (passwordConfirmationRequired) passwordConfirmationRequired.style.display = 'none';
            if (passwordHelpText) passwordHelpText.style.display = 'none';
            if (staffPasswordInfo) staffPasswordInfo.style.display = 'block';
        } else {
            // Guest accounts or editing: password is required
            if (isCreating) {
                if (passwordInput) passwordInput.setAttribute('required', 'required');
                if (passwordConfirmationInput) passwordConfirmationInput.setAttribute('required', 'required');
            }
            if (passwordRequired) passwordRequired.style.display = 'inline';
            if (passwordConfirmationRequired) passwordConfirmationRequired.style.display = 'inline';
            if (passwordHelpText) passwordHelpText.style.display = 'block';
            if (staffPasswordInfo) staffPasswordInfo.style.display = 'none';
        }
    }
    
    // Update on role change
    if (roleSelect) {
        roleSelect.addEventListener('change', updatePasswordRequirements);
        // Initial update
        updatePasswordRequirements();
    }

    // Toggle password visibility
    if (togglePasswordBtn) {
        togglePasswordBtn.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            togglePasswordIcon.classList.toggle('fa-eye');
            togglePasswordIcon.classList.toggle('fa-eye-slash');
        });
    }

    // Toggle password confirmation visibility
    if (togglePasswordConfirmationBtn && passwordConfirmationInput) {
        togglePasswordConfirmationBtn.addEventListener('click', function() {
            const type = passwordConfirmationInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordConfirmationInput.setAttribute('type', type);
            togglePasswordConfirmationIcon.classList.toggle('fa-eye');
            togglePasswordConfirmationIcon.classList.toggle('fa-eye-slash');
        });
    }

    // Password strength checker
    function checkPasswordStrength(password) {
        let strength = 0;
        let feedback = [];

        if (password.length === 0) {
            passwordStrengthDiv.style.display = 'none';
            return;
        }

        passwordStrengthDiv.style.display = 'block';

        // Length check
        if (password.length >= 8) {
            strength += 1;
        } else {
            feedback.push('At least 8 characters');
        }

        // Lowercase check
        if (/[a-z]/.test(password)) {
            strength += 1;
        } else {
            feedback.push('Lowercase letter');
        }

        // Uppercase check
        if (/[A-Z]/.test(password)) {
            strength += 1;
        } else {
            feedback.push('Uppercase letter');
        }

        // Number check
        if (/[0-9]/.test(password)) {
            strength += 1;
        } else {
            feedback.push('Number');
        }

        // Special character check
        if (/[^A-Za-z0-9]/.test(password)) {
            strength += 1;
        } else {
            feedback.push('Special character');
        }

        // Calculate percentage and set color
        const percentage = (strength / 5) * 100;
        passwordStrengthBar.style.width = percentage + '%';

        let strengthLabel = '';
        let strengthClass = '';

        if (strength <= 1) {
            strengthLabel = 'Very Weak';
            strengthClass = 'bg-danger';
        } else if (strength === 2) {
            strengthLabel = 'Weak';
            strengthClass = 'bg-warning';
        } else if (strength === 3) {
            strengthLabel = 'Fair';
            strengthClass = 'bg-info';
        } else if (strength === 4) {
            strengthLabel = 'Good';
            strengthClass = 'bg-primary';
        } else {
            strengthLabel = 'Strong';
            strengthClass = 'bg-success';
        }

        passwordStrengthBar.className = 'progress-bar ' + strengthClass;
        passwordStrengthText.textContent = 'Strength: ' + strengthLabel + (feedback.length > 0 ? ' - Add: ' + feedback.join(', ') : '');
        
        if (strength < 3) {
            passwordStrengthText.className = 'form-text mt-1 text-danger';
        } else if (strength < 5) {
            passwordStrengthText.className = 'form-text mt-1 text-warning';
        } else {
            passwordStrengthText.className = 'form-text mt-1 text-success';
        }
    }

    // Check password match
    function checkPasswordMatch() {
        if (!passwordConfirmationInput || !passwordInput) return;

        const password = passwordInput.value;
        const confirmation = passwordConfirmationInput.value;

        if (confirmation.length === 0) {
            passwordMatchText.textContent = '';
            passwordMatchText.className = 'form-text mt-1';
            return;
        }

        if (password === confirmation) {
            passwordMatchText.textContent = '✓ Passwords match';
            passwordMatchText.className = 'form-text mt-1 text-success';
        } else {
            passwordMatchText.textContent = '✗ Passwords do not match';
            passwordMatchText.className = 'form-text mt-1 text-danger';
        }
    }

    // Event listeners
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            checkPasswordStrength(this.value);
            if (passwordConfirmationInput) {
                checkPasswordMatch();
            }
        });
    }

    if (passwordConfirmationInput) {
        passwordConfirmationInput.addEventListener('input', function() {
            checkPasswordMatch();
        });
    }
});
</script>
@endsection

