@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-user"></i> My Profile</h1>
    <p>Manage your profile information</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="#">Profile</a></li>
  </ul>
</div>

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
  <strong>Error!</strong> Please correct the following errors:
  <ul class="mb-0">
    @foreach($errors->all() as $error)
    <li>{{ $error }}</li>
    @endforeach
  </ul>
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
@endif

<div class="row user">
  <div class="col-md-12">
    <div class="profile">
      <div class="info">
        <div class="profile-photo-container" style="position: relative; display: inline-block;">
          <img class="user-img" 
               src="{{ $user->profile_photo ? asset('storage/' . $user->profile_photo) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&size=128&background=007bff&color=fff' }}" 
               alt="User Image"
               id="profilePhotoHeaderPreview"
               style="width: 128px; height: 128px; border-radius: 50%; object-fit: cover; border: 3px solid #007bff;">
        </div>
        <h4>{{ $user->name }}</h4>
        <p style="text-transform: capitalize;">{{ $user instanceof \App\Models\Staff ? ($user->role ?? 'Manager') : 'Guest' }}</p>
      </div>
      <div class="cover-image"></div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="tile p-0">
      <ul class="nav flex-column nav-tabs user-tabs">
        <li class="nav-item">
          <a class="nav-link active" href="#user-profile" data-toggle="tab">
            <i class="fa fa-user"></i> Profile
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#user-password" data-toggle="tab">
            <i class="fa fa-lock"></i> Change Password
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#user-notifications" data-toggle="tab">
            <i class="fa fa-bell"></i> Notifications
          </a>
        </li>
      </ul>
    </div>
  </div>
  <div class="col-md-9">
    <div class="tab-content">
      <!-- Profile Tab -->
      <div class="tab-pane active" id="user-profile">
        <div class="tile user-settings">
          <h4 class="line-head"><i class="fa fa-user"></i> Profile Information</h4>
          
          <!-- Photo Upload Form -->
          @php
            // Determine the correct route based on the current route prefix or route name
            $currentRoute = request()->route()->getName() ?? '';
            $currentPath = request()->path();
            
            // Default routes
            $photoRoute = 'admin.profile.update-photo';
            $updateRoute = 'admin.profile.update';
            $passwordRoute = 'admin.profile.update-password';
            $notificationsRoute = 'admin.profile.update-notifications';
            
            // Check route name first (most reliable)
            if (str_contains($currentRoute, 'super_admin')) {
                $photoRoute = 'super_admin.profile.update-photo';
                $updateRoute = 'super_admin.profile.update';
                $passwordRoute = 'super_admin.profile.update-password';
                $notificationsRoute = 'super_admin.profile.update-notifications';
            } elseif (str_contains($currentRoute, 'reception')) {
                $photoRoute = 'reception.profile.update-photo';
                $updateRoute = 'reception.profile.update';
                $passwordRoute = 'reception.profile.update-password';
                $notificationsRoute = 'reception.profile.update-notifications';
            } elseif (str_contains($currentRoute, 'housekeeper')) {
                $photoRoute = 'housekeeper.profile.update-photo';
                $updateRoute = 'housekeeper.profile.update';
                $passwordRoute = 'housekeeper.profile.update-password';
                $notificationsRoute = 'housekeeper.profile.update-notifications';
            } elseif (str_contains($currentRoute, 'customer')) {
                $photoRoute = 'customer.profile.update-photo';
                $updateRoute = 'customer.profile.update';
                $passwordRoute = 'customer.profile.update-password';
                $notificationsRoute = 'customer.profile.update-notifications';
            } elseif (str_contains($currentRoute, 'admin')) {
                $photoRoute = 'admin.profile.update-photo';
                $updateRoute = 'admin.profile.update';
                $passwordRoute = 'admin.profile.update-password';
                $notificationsRoute = 'admin.profile.update-notifications';
            }
            // Fallback to URL path check
            elseif (str_contains($currentPath, 'super-admin')) {
                $photoRoute = 'super_admin.profile.update-photo';
                $updateRoute = 'super_admin.profile.update';
                $passwordRoute = 'super_admin.profile.update-password';
                $notificationsRoute = 'super_admin.profile.update-notifications';
            } elseif (str_contains($currentPath, 'manager') || str_contains($currentPath, 'admin')) {
                $photoRoute = 'admin.profile.update-photo';
                $updateRoute = 'admin.profile.update';
                $passwordRoute = 'admin.profile.update-password';
                $notificationsRoute = 'admin.profile.update-notifications';
            } elseif (str_contains($currentPath, 'reception')) {
                $photoRoute = 'reception.profile.update-photo';
                $updateRoute = 'reception.profile.update';
                $passwordRoute = 'reception.profile.update-password';
                $notificationsRoute = 'reception.profile.update-notifications';
            } elseif (str_contains($currentPath, 'housekeeper')) {
                $photoRoute = 'housekeeper.profile.update-photo';
                $updateRoute = 'housekeeper.profile.update';
                $passwordRoute = 'housekeeper.profile.update-password';
                $notificationsRoute = 'housekeeper.profile.update-notifications';
            } elseif (str_contains($currentPath, 'customer')) {
                $photoRoute = 'customer.profile.update-photo';
                $updateRoute = 'customer.profile.update';
                $passwordRoute = 'customer.profile.update-password';
                $notificationsRoute = 'customer.profile.update-notifications';
            }
          @endphp
          
          <!-- Photo Upload Section -->
          <div class="row mb-4">
            <div class="col-md-12">
              <label><i class="fa fa-camera"></i> Profile Photo</label>
              <div class="photo-upload-section" style="display: flex; align-items: center; gap: 20px; margin-top: 10px;">
                <div class="photo-preview-container" style="position: relative; display: inline-block;">
                  <img class="user-img" 
                       src="{{ $user->profile_photo ? asset('storage/' . $user->profile_photo) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&size=128&background=007bff&color=fff' }}" 
                       alt="User Image"
                       id="profilePhotoPreview"
                       style="width: 128px; height: 128px; border-radius: 50%; object-fit: cover; border: 3px solid #007bff;">
                  <label for="profilePhotoInput" class="photo-upload-overlay" style="position: absolute; bottom: 0; right: 0; background: #007bff; color: white; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; cursor: pointer; border: 3px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
                    <i class="fa fa-camera" style="font-size: 18px;"></i>
                  </label>
                  <input type="file" id="profilePhotoInput" name="profile_photo" accept="image/*" style="display: none;">
                </div>
                <div class="photo-upload-controls" style="flex: 1;">
                  <form id="photoForm" action="{{ route($photoRoute) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="profile_photo" id="photoFormInput" accept="image/*" style="display: none;">
                    <div id="photoPreviewInfo" style="display: none; margin-bottom: 15px;">
                      <p style="margin: 0; color: #666; font-size: 14px;">
                        <i class="fa fa-info-circle"></i> New photo selected. Click "Update Photo" to save.
                      </p>
                    </div>
                    <div id="photoUploadButtons" style="display: none;">
                      <button type="submit" class="btn btn-primary" id="updatePhotoBtn">
                        <i class="fa fa-upload"></i> Update Photo
                      </button>
                      <button type="button" class="btn btn-secondary" id="cancelPhotoBtn" style="margin-left: 10px;">
                        <i class="fa fa-times"></i> Cancel
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Profile Update Form -->
          <form action="{{ route($updateRoute) }}" method="POST">
            @csrf
            <div class="row mb-4">
              <div class="col-md-6">
                <label>Full Name <span class="text-danger">*</span></label>
                <input class="form-control" type="text" name="name" value="{{ old('name', $user->name) }}" required>
                @error('name')
                <small class="text-danger">{{ $message }}</small>
                @enderror
              </div>
              <div class="col-md-6">
                <label>Email <span class="text-danger">*</span></label>
                <input class="form-control" type="email" name="email" value="{{ old('email', $user->email) }}" required>
                @error('email')
                <small class="text-danger">{{ $message }}</small>
                @enderror
              </div>
            </div>
            <div class="row mb-4">
              <div class="col-md-6">
                <label>Role</label>
                <input class="form-control" type="text" value="{{ $user instanceof \App\Models\Staff ? ucfirst($user->role ?? 'Manager') : 'Guest' }}" disabled>
              </div>
              <div class="col-md-6">
                <label>Registered Date</label>
                <input class="form-control" type="text" value="{{ $user->created_at->format('M d, Y') }}" disabled>
              </div>
            </div>
            <div class="row mb-10">
              <div class="col-md-12">
                <button class="btn btn-primary" type="submit">
                  <i class="fa fa-fw fa-lg fa-check-circle"></i> Update Profile
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
      
      <!-- Change Password Tab -->
      <div class="tab-pane fade" id="user-password">
        <div class="tile user-settings">
          <h4 class="line-head"><i class="fa fa-lock"></i> Change Password</h4>
          <form action="{{ route($passwordRoute) }}" method="POST">
            @csrf
            <div class="row mb-4">
              <div class="col-md-8">
                <label>Current Password <span class="text-danger">*</span></label>
                <div class="input-group">
                  <input class="form-control" type="password" name="current_password" id="current_password" required>
                  <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="button" id="toggleCurrentPassword" 
                            style="border-top-right-radius: 0.25rem; border-bottom-right-radius: 0.25rem;">
                      <i class="fa fa-eye" id="toggleCurrentPasswordIcon"></i>
                    </button>
                  </div>
                </div>
                @error('current_password')
                <small class="text-danger">{{ $message }}</small>
                @enderror
              </div>
            </div>
            <div class="row mb-4">
              <div class="col-md-8">
                <label>New Password <span class="text-danger">*</span></label>
                <div class="input-group">
                  <input class="form-control" type="password" name="new_password" id="new_password" required minlength="8">
                  <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="button" id="toggleNewPassword" 
                            style="border-top-right-radius: 0.25rem; border-bottom-right-radius: 0.25rem;">
                      <i class="fa fa-eye" id="toggleNewPasswordIcon"></i>
                    </button>
                  </div>
                </div>
                <small class="text-muted">Password must be at least 8 characters long.</small>
                @error('new_password')
                <small class="text-danger d-block">{{ $message }}</small>
                @enderror
                <!-- Password Strength Indicator -->
                <div id="passwordStrength" class="mt-2" style="display: none;">
                  <div class="progress" style="height: 5px;">
                    <div id="passwordStrengthBar" class="progress-bar" role="progressbar" 
                         style="width: 0%; transition: width 0.3s ease, background-color 0.3s ease;"></div>
                  </div>
                  <small id="passwordStrengthText" class="form-text mt-1"></small>
                </div>
              </div>
            </div>
            <div class="row mb-4">
              <div class="col-md-8">
                <label>Confirm New Password <span class="text-danger">*</span></label>
                <div class="input-group">
                  <input class="form-control" type="password" name="new_password_confirmation" id="new_password_confirmation" required minlength="8">
                  <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword" 
                            style="border-top-right-radius: 0.25rem; border-bottom-right-radius: 0.25rem;">
                      <i class="fa fa-eye" id="toggleConfirmPasswordIcon"></i>
                    </button>
                  </div>
                </div>
                @error('new_password_confirmation')
                <small class="text-danger">{{ $message }}</small>
                @enderror
                <!-- Password Match Indicator -->
                <small id="passwordMatchText" class="form-text mt-1"></small>
              </div>
            </div>
            <div class="row mb-10">
              <div class="col-md-12">
                <button class="btn btn-primary" type="submit">
                  <i class="fa fa-fw fa-lg fa-check-circle"></i> Update Password
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
      
      <!-- Notification Settings Tab -->
      <div class="tab-pane fade" id="user-notifications">
        <div class="tile user-settings">
          <h4 class="line-head"><i class="fa fa-bell"></i> Notification Settings</h4>
          <p class="text-muted mb-4">Manage your email notification preferences. All notifications are enabled by default.</p>
          
          @php
            $prefs = $user->notification_preferences ?? [
              'email_notifications_enabled' => true,
              'service_request_notifications' => true,
              'issue_report_notifications' => true,
              'booking_notifications' => true,
              'payment_notifications' => true,
              'check_in_out_notifications' => true,
              'extension_request_notifications' => true,
            ];
          @endphp
          
          <form action="{{ route($notificationsRoute) }}" method="POST" id="notificationsForm">
            @csrf
            <div class="row mb-4">
              <div class="col-md-12">
                <div class="form-group">
                  <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="email_notifications_enabled" 
                           name="email_notifications_enabled" value="1" 
                           {{ ($prefs['email_notifications_enabled'] ?? true) ? 'checked' : '' }}
                           onchange="toggleAllNotifications(this.checked)">
                    <label class="custom-control-label" for="email_notifications_enabled">
                      <strong>Enable Email Notifications</strong>
                      <small class="d-block text-muted">Master switch for all email notifications</small>
                    </label>
                  </div>
                </div>
              </div>
            </div>
            
            <hr>
            
            <h5 class="mb-3">Notification Types</h5>
            
            <div class="row mb-3">
              <div class="col-md-12">
                <div class="form-group">
                  <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input notification-checkbox" 
                           id="service_request_notifications" name="service_request_notifications" value="1" 
                           {{ ($prefs['service_request_notifications'] ?? true) ? 'checked' : '' }}
                           {{ !($prefs['email_notifications_enabled'] ?? true) ? 'disabled' : '' }}>
                    <label class="custom-control-label" for="service_request_notifications">
                      Service Request Notifications
                      <small class="d-block text-muted">Get notified about service requests and their status changes</small>
                    </label>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="row mb-3">
              <div class="col-md-12">
                <div class="form-group">
                  <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input notification-checkbox" 
                           id="issue_report_notifications" name="issue_report_notifications" value="1" 
                           {{ ($prefs['issue_report_notifications'] ?? true) ? 'checked' : '' }}
                           {{ !($prefs['email_notifications_enabled'] ?? true) ? 'disabled' : '' }}>
                    <label class="custom-control-label" for="issue_report_notifications">
                      Issue Report Notifications
                      <small class="d-block text-muted">Get notified about new issues and their status updates</small>
                    </label>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="row mb-3">
              <div class="col-md-12">
                <div class="form-group">
                  <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input notification-checkbox" 
                           id="booking_notifications" name="booking_notifications" value="1" 
                           {{ ($prefs['booking_notifications'] ?? true) ? 'checked' : '' }}
                           {{ !($prefs['email_notifications_enabled'] ?? true) ? 'disabled' : '' }}>
                    <label class="custom-control-label" for="booking_notifications">
                      Booking Notifications
                      <small class="d-block text-muted">Get notified about new bookings</small>
                    </label>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="row mb-3">
              <div class="col-md-12">
                <div class="form-group">
                  <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input notification-checkbox" 
                           id="payment_notifications" name="payment_notifications" value="1" 
                           {{ ($prefs['payment_notifications'] ?? true) ? 'checked' : '' }}
                           {{ !($prefs['email_notifications_enabled'] ?? true) ? 'disabled' : '' }}>
                    <label class="custom-control-label" for="payment_notifications">
                      Payment Notifications
                      <small class="d-block text-muted">Get notified when payments are received</small>
                    </label>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="row mb-3">
              <div class="col-md-12">
                <div class="form-group">
                  <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input notification-checkbox" 
                           id="check_in_out_notifications" name="check_in_out_notifications" value="1" 
                           {{ ($prefs['check_in_out_notifications'] ?? true) ? 'checked' : '' }}
                           {{ !($prefs['email_notifications_enabled'] ?? true) ? 'disabled' : '' }}>
                    <label class="custom-control-label" for="check_in_out_notifications">
                      Check-in/Check-out Notifications
                      <small class="d-block text-muted">Get notified when guests check in or check out</small>
                    </label>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="row mb-3">
              <div class="col-md-12">
                <div class="form-group">
                  <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input notification-checkbox" 
                           id="extension_request_notifications" name="extension_request_notifications" value="1" 
                           {{ ($prefs['extension_request_notifications'] ?? true) ? 'checked' : '' }}
                           {{ !($prefs['email_notifications_enabled'] ?? true) ? 'disabled' : '' }}>
                    <label class="custom-control-label" for="extension_request_notifications">
                      Extension Request Notifications
                      <small class="d-block text-muted">Get notified about booking extension requests</small>
                    </label>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="row mb-10">
              <div class="col-md-12">
                <button class="btn btn-primary" type="submit">
                  <i class="fa fa-fw fa-lg fa-check-circle"></i> Save Notification Settings
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  .profile-photo-container {
    margin-bottom: 20px;
  }
  
  .photo-upload-overlay:hover {
    background: #0056b3 !important;
    transform: scale(1.1);
    transition: all 0.3s ease;
  }
  
  .user-img {
    transition: all 0.3s ease;
  }
  
  .user-img:hover {
    opacity: 0.9;
  }
  
  .nav-tabs .nav-link {
    color: #495057;
    padding: 12px 20px;
  }
  
  .nav-tabs .nav-link.active {
    color: #007bff;
    background-color: #f8f9fa;
    border-color: #dee2e6 #dee2e6 #fff;
  }
  
  .nav-tabs .nav-link:hover {
    border-color: #e9ecef #e9ecef #dee2e6;
  }
  
  .user-settings {
    padding: 20px;
  }
  
  .line-head {
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #e9ecef;
  }
  
  .photo-upload-section {
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #e9ecef;
  }
  
  #photoPreviewInfo {
    padding: 10px 15px;
    background: #e7f3ff;
    border-left: 4px solid #007bff;
    border-radius: 4px;
  }
  
  #photoPreviewInfo p {
    margin: 0;
    color: #004085;
  }
  
  #photoUploadButtons {
    margin-top: 10px;
  }
  
  #updatePhotoBtn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
  }
  
  /* Password Toggle Button */
  .input-group-append .btn {
    border-left: 0;
  }
  
  .input-group-append .btn:hover {
    background-color: #e9ecef;
  }
  
  /* Password Strength Indicator */
  #passwordStrength {
    margin-top: 10px;
  }
  
  #passwordStrengthBar {
    transition: width 0.3s ease, background-color 0.3s ease;
  }
  
  #passwordStrengthText {
    font-size: 12px;
    font-weight: 500;
  }
  
  #passwordMatchText {
    font-size: 12px;
    font-weight: 500;
  }
</style>

<script>
  let selectedFile = null;
  
  // Handle profile photo selection
  document.getElementById('profilePhotoInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
      // Validate file type
      if (!file.type.match('image.*')) {
        alert('Please select an image file.');
        this.value = '';
        return;
      }
      
      // Validate file size (max 2MB)
      if (file.size > 2048 * 1024) {
        alert('Image size must be less than 2MB.');
        this.value = '';
        return;
      }
      
      // Store the selected file
      selectedFile = file;
      
      // Preview image
      const reader = new FileReader();
      reader.onload = function(e) {
        // Update preview in profile section
        document.getElementById('profilePhotoPreview').src = e.target.result;
        // Update preview in header (top profile section)
        const headerPreview = document.getElementById('profilePhotoHeaderPreview');
        if (headerPreview) {
          headerPreview.src = e.target.result;
        }
        // Also update sidebar photo immediately for better UX
        const sidebarAvatar = document.getElementById('sidebarUserAvatar');
        if (sidebarAvatar) {
          sidebarAvatar.src = e.target.result;
        }
      };
      reader.readAsDataURL(file);
      
      // Copy file to form input
      const formInput = document.getElementById('photoFormInput');
      const dataTransfer = new DataTransfer();
      dataTransfer.items.add(file);
      formInput.files = dataTransfer.files;
      
      // Show preview info and update buttons
      document.getElementById('photoPreviewInfo').style.display = 'block';
      document.getElementById('photoUploadButtons').style.display = 'block';
    }
  });
  
  // Handle photo form submission
  document.getElementById('photoForm').addEventListener('submit', function(e) {
    if (!selectedFile) {
      e.preventDefault();
      alert('Please select a photo first.');
      return false;
    }
    // Show loading state
    const updateBtn = document.getElementById('updatePhotoBtn');
    updateBtn.disabled = true;
    updateBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Updating...';
  });
  
  // Handle cancel button
  document.getElementById('cancelPhotoBtn').addEventListener('click', function() {
    // Reset to original photo
    @php
      $originalPhoto = $user->profile_photo 
        ? asset('storage/' . $user->profile_photo) 
        : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&size=128&background=007bff&color=fff';
    @endphp
    const originalPhoto = '{{ $originalPhoto }}';
    
    document.getElementById('profilePhotoPreview').src = originalPhoto;
    const headerPreview = document.getElementById('profilePhotoHeaderPreview');
    if (headerPreview) {
      headerPreview.src = originalPhoto;
    }
    const sidebarAvatar = document.getElementById('sidebarUserAvatar');
    if (sidebarAvatar) {
      sidebarAvatar.src = originalPhoto;
    }
    
    // Reset file inputs
    document.getElementById('profilePhotoInput').value = '';
    document.getElementById('photoFormInput').value = '';
    selectedFile = null;
    
    // Hide preview info and buttons
    document.getElementById('photoPreviewInfo').style.display = 'none';
    document.getElementById('photoUploadButtons').style.display = 'none';
    
    // Re-enable update button if it was disabled
    const updateBtn = document.getElementById('updatePhotoBtn');
    updateBtn.disabled = false;
    updateBtn.innerHTML = '<i class="fa fa-upload"></i> Update Photo';
  });
  
  // Trigger file input when overlay is clicked
  document.querySelector('.photo-upload-overlay').addEventListener('click', function() {
    document.getElementById('profilePhotoInput').click();
  });
  
  // Password Toggle Functionality
  document.addEventListener('DOMContentLoaded', function() {
    // Current Password Toggle
    const currentPasswordInput = document.getElementById('current_password');
    const toggleCurrentPasswordBtn = document.getElementById('toggleCurrentPassword');
    const toggleCurrentPasswordIcon = document.getElementById('toggleCurrentPasswordIcon');
    
    if (toggleCurrentPasswordBtn && currentPasswordInput) {
      toggleCurrentPasswordBtn.addEventListener('click', function() {
        const type = currentPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        currentPasswordInput.setAttribute('type', type);
        toggleCurrentPasswordIcon.classList.toggle('fa-eye');
        toggleCurrentPasswordIcon.classList.toggle('fa-eye-slash');
      });
    }
    
    // New Password Toggle
    const newPasswordInput = document.getElementById('new_password');
    const toggleNewPasswordBtn = document.getElementById('toggleNewPassword');
    const toggleNewPasswordIcon = document.getElementById('toggleNewPasswordIcon');
    
    if (toggleNewPasswordBtn && newPasswordInput) {
      toggleNewPasswordBtn.addEventListener('click', function() {
        const type = newPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        newPasswordInput.setAttribute('type', type);
        toggleNewPasswordIcon.classList.toggle('fa-eye');
        toggleNewPasswordIcon.classList.toggle('fa-eye-slash');
      });
    }
    
    // Confirm Password Toggle
    const confirmPasswordInput = document.getElementById('new_password_confirmation');
    const toggleConfirmPasswordBtn = document.getElementById('toggleConfirmPassword');
    const toggleConfirmPasswordIcon = document.getElementById('toggleConfirmPasswordIcon');
    
    if (toggleConfirmPasswordBtn && confirmPasswordInput) {
      toggleConfirmPasswordBtn.addEventListener('click', function() {
        const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        confirmPasswordInput.setAttribute('type', type);
        toggleConfirmPasswordIcon.classList.toggle('fa-eye');
        toggleConfirmPasswordIcon.classList.toggle('fa-eye-slash');
      });
    }
    
    // Password Strength Indicator
    const passwordStrengthDiv = document.getElementById('passwordStrength');
    const passwordStrengthBar = document.getElementById('passwordStrengthBar');
    const passwordStrengthText = document.getElementById('passwordStrengthText');
    const passwordMatchText = document.getElementById('passwordMatchText');
    
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
      if (!confirmPasswordInput || !newPasswordInput) return;
      
      const password = newPasswordInput.value;
      const confirmation = confirmPasswordInput.value;
      
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
    
    // Event listeners for password strength and match
    if (newPasswordInput) {
      newPasswordInput.addEventListener('input', function() {
        checkPasswordStrength(this.value);
        if (confirmPasswordInput) {
          checkPasswordMatch();
        }
      });
    }
    
    if (confirmPasswordInput) {
      confirmPasswordInput.addEventListener('input', function() {
        checkPasswordMatch();
      });
    }
  });
  
  // Notification Settings Toggle
  function toggleAllNotifications(enabled) {
    const checkboxes = document.querySelectorAll('.notification-checkbox');
    checkboxes.forEach(function(checkbox) {
      checkbox.disabled = !enabled;
      if (!enabled) {
        checkbox.checked = false;
      }
    });
  }
  
  // Handle notifications form submission
  document.getElementById('notificationsForm')?.addEventListener('submit', function(e) {
    const submitBtn = this.querySelector('button[type="submit"]');
    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Saving...';
    }
  });
</script>
@endsection

