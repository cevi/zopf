<nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
    <div class="container">

        @auth
            <a class="navbar-brand" href="{{ url('/home') }}">
                <img src="/img/logo.svg" alt="..." style="width: 20rem" class="img-fluid">
            </a>
        @else
            <a class="navbar-brand" href="{{ url('/') }}">
                <img src="/img/logo.svg" alt="..." style="width: 20rem" class="img-fluid">
            </a>
        @endauth
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav mr-auto">
                @auth
                    @if (Auth::user()->isActionleader() && Auth::user()->action)
                        <li>
                            <a class="nav-link nav-item" href="/admin">Dashboard<span class="caret"></span></a>
                        </li>
                    @endif
                @endauth

            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ml-auto">
                <!-- Authentication Links -->
                @auth
                    @if(Auth::user()->email_verified_at)
                        <x-groups-dropdown/>
                        <x-actions-dropdown/>
                    @endif
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
