<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Main CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboard_assets/css/main.css') }}">
    <!-- Font-icon css-->
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Login - {{ config('app.name') }}</title>
  </head>
  <body>
    <!-- Mobile Header with Back Button -->
    <div class="mobile-login-header" style="background-color: #940000 !important;">
      <h2 class="mobile-header-title" style="color: #ffffff !important;">{{ config('app.name') }}</h2>
    </div>
    
    <section class="material-half-bg">
      <div class="cover"></div>
    </section>
    <section class="login-content">
      <!-- Return to Home removed -->
      
      <!-- Success/Error Messages -->
      @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="margin-bottom: 20px; border-radius: 6px;">
          <i class="fa fa-check-circle"></i> {{ session('success') }}
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      @endif
      
      @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin-bottom: 20px; border-radius: 6px;">
          <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      @endif
      
      <div class="logo">
        <h1 style="color: #940000 !important;">{{ config('app.name') }}</h1>
      </div>
      <div class="login-box">
        <form class="login-form" action="{{ route('login.post') }}" method="POST">
          @csrf
          <h3 class="login-head"><i class="fa fa-lg fa-fw fa-user"></i>SIGN IN</h3>

          <div class="form-group">
            <label class="control-label">EMAIL</label>
            <input class="form-control @error('email') is-invalid @enderror" type="email" name="email" placeholder="Email" value="{{ old('email') }}" autofocus required>
            @error('email')
              <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
              </span>
            @enderror
          </div>
          <div class="form-group" id="password-section">
            <label class="control-label">PASSWORD</label>
            <div class="input-group">
              <input class="form-control @error('password') is-invalid @enderror" type="password" name="password" id="password" placeholder="Password" required>
              <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="button" id="togglePassword" style="border-top-right-radius: 0.25rem; border-bottom-right-radius: 0.25rem; border-left: none;">
                  <i class="fa fa-eye" id="togglePasswordIcon"></i>
                </button>
              </div>
            </div>
            @error('password')
              <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
              </span>
            @enderror
          </div>
          <div class="form-group" id="remember-section">
            <div class="utility">
              <div class="animated-checkbox">
                <label>
                  <input type="checkbox" name="remember"><span class="label-text">Stay Signed in</span>
                </label>
              </div>
              <p class="semibold-text mb-2"><a href="#" data-toggle="flip">Forgot Password ?</a></p>
            </div>
          </div>
          <div class="form-group btn-container">
            <button type="submit" id="login-btn" class="btn btn-primary btn-block">
              <span id="login-btn-text"><i class="fa fa-sign-in fa-lg fa-fw"></i>SIGN IN</span>
              <span id="login-btn-spinner" style="display: none;">
                <i class="fa fa-spinner fa-spin fa-lg fa-fw"></i> SIGNING IN...
              </span>
            </button>
          </div>
        </form>
        <form class="forget-form" action="{{ route('password.forgot') }}" method="POST">
          @csrf
          <h3 class="login-head"><i class="fa fa-lg fa-fw fa-lock"></i>Forgot Password ?</h3>
          <div class="form-group">
            <label class="control-label">EMAIL</label>
            <input class="form-control @error('email') is-invalid @enderror" type="email" name="email" placeholder="Email" value="{{ old('email') }}" required autofocus>
            @error('email')
              <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
              </span>
            @enderror
          </div>
          <div class="form-group btn-container">
            <button type="submit" id="reset-btn" class="btn btn-primary btn-block">
              <span id="reset-btn-text"><i class="fa fa-unlock fa-lg fa-fw"></i>RESET PASSWORD</span>
              <span id="reset-btn-spinner" style="display: none;">
                <i class="fa fa-spinner fa-spin fa-lg fa-fw"></i> SENDING...
              </span>
            </button>
          </div>
          <div class="form-group mt-3">
            <p class="semibold-text mb-0"><a href="#" data-toggle="flip"><i class="fa fa-angle-left fa-fw"></i> Back to Login</a></p>
          </div>
        </form>

      </div>
      
      <!-- Powered By Footer -->
      <div class="login-footer" style="text-align: center; margin-top: 30px; padding: 20px 0; color: rgba(0,0,0,0.6); font-size: 14px;">
        <p style="margin: 0;">
          Powered By <a href="https://emca.tech/#" target="_blank" style="color: #940000; font-weight: 600; text-decoration: none;">EmCa Techonologies</a>
        </p>
      </div>
    </section>
    <!-- Essential javascripts for application to work-->
    <script src="{{ asset('dashboard_assets/js/jquery-3.2.1.min.js') }}"></script>
    <script src="{{ asset('dashboard_assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('dashboard_assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('dashboard_assets/js/main.js') }}"></script>
    <!-- The javascript plugin to display page loading on top-->
    <script src="{{ asset('dashboard_assets/js/plugins/pace.min.js') }}"></script>
    <style>
      @font-face {
        font-family: 'Century Gothic';
        src: local('Century Gothic'), local('CenturyGothic'), url('https://fonts.cdnfonts.com/s/15454/GOTHIC.woff') format('woff');
      }

      /* Background Image */
      .material-half-bg {
        background-image: url('{{ asset("dashboard_assets/images/restaurant-interior.jpg") }}');
        background-size: cover !important;
        background-position: center center !important;
        background-repeat: no-repeat !important;
        background-color: #e7e7e7 !important;
        width: 100% !important;
        height: 100vh !important;
      }
      
      .material-half-bg .cover {
        background: rgba(0,0,0,0.4) !important;
        height: 100vh !important;
        width: 100% !important;
      }
      
      /* Return to Home Arrow */
      .return-home-arrow {
        position: fixed;
        left: 30px;
        top: 50%;
        transform: translateY(-50%);
        width: 50px;
        height: 50px;
        background-color: rgba(26, 54, 93, 0.9);
        color: #ffffff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        text-decoration: none;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
        z-index: 1000;
      }
      
      .return-home-arrow:hover {
        background-color: rgba(26, 54, 93, 1);
        transform: translateY(-50%) translateX(-5px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
        text-decoration: none;
        color: #ffffff;
      }
      
      .return-home-arrow i {
        margin-left: -2px;
      }
      
      /* Mobile Header with Back Button */
      .mobile-login-header {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        height: 60px;
        background-color: rgba(26, 54, 93, 0.95);
        backdrop-filter: blur(10px);
        z-index: 1001;
        display: flex;
        align-items: center;
        padding: 0 15px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
      }
      
      .mobile-back-button {
        width: 40px;
        height: 40px;
        background-color: rgba(255, 255, 255, 0.2);
        color: #ffffff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        text-decoration: none;
        transition: all 0.3s ease;
        margin-right: 15px;
      }
      
      .mobile-back-button:hover {
        background-color: rgba(255, 255, 255, 0.3);
        transform: translateX(-3px);
        text-decoration: none;
        color: #ffffff;
      }
      
      .mobile-header-title {
        color: #ffffff;
        font-size: 18px;
        font-weight: 600;
        letter-spacing: 0.5px;
        margin: 0;
        flex: 1;
        text-align: center;
      }
      
      /* Hide mobile header on desktop */
      @media (min-width: 769px) {
        .mobile-login-header {
          display: none !important;
        }
      }
      
      /* Hide desktop back button on mobile */
      @media (max-width: 768px) {
        .return-home-arrow {
          display: none !important;
        }
        
        .login-content {
          padding-top: 80px;
        }
      }
      
      /* Logo Styling */
      .logo h1 {
        color: #940000;
        font-weight: 600;
        letter-spacing: 1px;
      }
      
      .btn-primary {
        background-color: #940000 !important;
        border-color: #940000 !important;
      }
      
      .btn-primary:hover {
        background-color: #7b0000 !important;
        border-color: #7b0000 !important;
      }
      

      body {
        font-family: 'Century Gothic', 'Segoe UI', Tahoma, sans-serif !important;
      }
      
      /* Hide logo on mobile since it's in header */
      @media (max-width: 768px) {
        .logo {
          display: none;
        }
      }
      
      /* Button Loading State */
      #login-btn:disabled,
      #reset-btn:disabled {
        opacity: 0.7;
        cursor: not-allowed;
      }
      
      #login-btn-spinner,
      #reset-btn-spinner {
        display: inline-block;
      }
      
      /* Login Footer Styling */
      .login-footer {
        position: relative;
        z-index: 10;
      }
      
      .login-footer a:hover {
        text-decoration: underline;
        color: #cc0000 !important;
      }
      
      @media (max-width: 768px) {
        .login-footer {
          margin-top: 20px;
          padding: 15px 0;
          font-size: 12px;
        }
      }
    </style>
    <script type="text/javascript">
      // Login Page Flipbox control
      $('.login-content [data-toggle="flip"]').click(function() {
      	$('.login-box').toggleClass('flipped');
      	return false;
      });
      
      // Auto-flip to forgot password form if there are errors or flag is set
      $(document).ready(function() {
        @if(session('show_forgot_password') || ($errors->has('email') && old('email')))
          $('.login-box').addClass('flipped');
        @endif
        

        // Password Toggle Functionality
        $('#togglePassword').on('click', function() {
          const passwordInput = $('#password');
          const passwordIcon = $('#togglePasswordIcon');
          
          if (passwordInput.attr('type') === 'password') {
            passwordInput.attr('type', 'text');
            passwordIcon.removeClass('fa-eye').addClass('fa-eye-slash');
          } else {
            passwordInput.attr('type', 'password');
            passwordIcon.removeClass('fa-eye-slash').addClass('fa-eye');
          }
        });
      });
      
      // Login Form Submission with Loading Spinner
      $('.login-form').on('submit', function(e) {
        const $btn = $('#login-btn');
        const $btnText = $('#login-btn-text');
        const $btnSpinner = $('#login-btn-spinner');
        
        // Disable button and show spinner
        $btn.prop('disabled', true);
        $btnText.hide();
        $btnSpinner.show();
        
        // Re-enable after 30 seconds as fallback (in case of error)
        setTimeout(function() {
          $btn.prop('disabled', false);
          $btnText.show();
          $btnSpinner.hide();
        }, 30000);
      });
      
      // Reset Password Form Submission with Loading Spinner
      $('.forget-form').on('submit', function(e) {
        const $btn = $('#reset-btn');
        const $btnText = $('#reset-btn-text');
        const $btnSpinner = $('#reset-btn-spinner');
        
        // Disable button and show spinner
        $btn.prop('disabled', true);
        $btnText.hide();
        $btnSpinner.show();
        
        // Re-enable after 30 seconds as fallback (in case of error)
        setTimeout(function() {
          $btn.prop('disabled', false);
          $btnText.show();
          $btnSpinner.hide();
        }, 30000);
      });

    </script>
  </body>
</html>

