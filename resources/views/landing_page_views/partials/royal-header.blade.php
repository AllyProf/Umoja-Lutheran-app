<header class="header_area">
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light">
            <!-- Brand and toggle get grouped for better mobile display -->
            <a class="navbar-brand logo_h" href="{{ url('/') }}">
                <img src="{{ asset('royal-master/image/logo/Logo.png') }}" alt="PrimeLand Hotel Logo" style="height: 100px; width: auto; max-width: 400px;">
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse offset" id="navbarSupportedContent">
                <div class="mobile-menu-header d-lg-none">
                    <img src="{{ asset('royal-master/image/logo/Logo.png') }}" alt="PrimeLand Hotel" class="mobile-menu-logo">
                    <div class="mobile-menu-info">
                        <h3 class="mobile-menu-title">PrimeLand Hotel</h3>
                        <p class="mobile-menu-subtitle">Comfort in every stay</p>
                    </div>
                </div>
                <ul class="nav navbar-nav menu_nav ml-auto">
                    <li class="nav-item {{ request()->is('/') ? 'active' : '' }}"><a class="nav-link" href="{{ url('/') }}">Home</a></li> 
                    <li class="nav-item {{ request()->is('about-us') ? 'active' : '' }}"><a class="nav-link" href="{{ url('/about-us') }}">About us</a></li>
                    <li class="nav-item {{ request()->routeIs('booking.index') ? 'active' : '' }}"><a class="nav-link" href="{{ route('booking.index') }}">Book Now</a></li>
                    <li class="nav-item {{ request()->is('services') ? 'active' : '' }}"><a class="nav-link" href="{{ url('/services') }}">Services</a></li>
                    <li class="nav-item {{ request()->is('contact') ? 'active' : '' }}"><a class="nav-link" href="{{ url('/contact') }}">Contact</a></li>
                    <li class="nav-item {{ request()->is('login') ? 'active' : '' }}"><a class="nav-link" href="{{ url('/login') }}">Login</a></li>
                </ul>
            </div> 
        </nav>
    </div>
</header>
