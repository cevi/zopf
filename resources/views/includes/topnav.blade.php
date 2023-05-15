<nav class="navbar navbar-expand-md shadow-sm border-gray-200">
    <div class="container justify-between items-center mx-auto max-w-screen-xl px-4 md:px-6 py-2.5">

        @auth
            <a class="navbar-brand" href="{{ url('/home') }}">
                <img src="/img/logo.svg" alt="..." style="width: 20rem" class="img-fluid">
            </a>
        @else
            <a class="navbar-brand" href="{{ url('/') }}">
                <img src="/img/logo.svg" alt="..." style="width: 20rem" class="img-fluid">
            </a>
        @endauth
        <button data-collapse-toggle="mega-menu-full" type="button"
                class="inline-flex items-center p-2 ml-1 text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600"
                aria-controls="mega-menu-full" aria-expanded="false">
            <span class="sr-only">Hauptmenü</span>
            <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20"
                 xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd"
                      d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                      clip-rule="evenodd"></path>
            </svg>
        </button>

        <div id="mega-menu-full" class="hidden justify-between items-center w-full md:flex md:w-auto md:order-1">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav ml-auto">
                @auth
                    @if (Auth::user()->isActionleader() && Auth::user()->action && !Auth::user()->action->global)
                        <li>
                            <a class="nav-link nav-item" href="/admin">Dashboard<span class="caret"></span></a>
                        </li>
                    @endif
                    @if(Auth::user()->email_verified_at)
                        <x-groups-dropdown/>
                        @if (Auth::user()->group && !Auth::user()->group->global)
                            <x-actions-dropdown/>
                        @endif
                    @endif
                @endauth
                <x-users-dropdown/>
            </ul>
        </div>
    </div>
</nav>
