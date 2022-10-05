<header class="header">
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
        <div class="container-fluid">
            <div class="navbar-holder d-flex align-items-center justify-content-between">
                <div class="navbar-nav navbar-header">
                    <a id="toggle-btn" href="#" class="menu-btn">
                        <i class="fas fa-bars"></i> </a>
                </div>
                <div class="navbar-nav navbar-header">
                    <a href="{{ url('/admin') }}">
                        <img src="/img/logo.svg" alt="..." style="width: 20rem" class="img-fluid">
                    </a>
                </div>
                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ml-auto">
                    <!-- Authentication Links -->
                    @auth
                        <x-groups-dropdown/>
                        <x-actions-dropdown/>
                        <x-users-dropdown/>
                    @else
                        <li>
                            <a href="{{ route('login') }}" class="nav-link nav-item">Login</a>
                        </li>
                        @if (Route::has('register'))
                            <li>
                                <a href="{{ route('register') }}" class="nav-link nav-item">Registrieren</a>
                            </li>
                        @endif
                    @endauth
                </ul>
            </div>
        </div>
    </nav>
</header>
