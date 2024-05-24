<nav
    class="dark:bg-gray-800 bg-gray-100 border-b border-gray-200 px-4 lg:px-6 py-2.5 dark:border-gray-700 fixed left-0 right-0 top-0 z-50">
    <div class="flex flex-wrap justify-between items-center">
        <div class="flex justify-start items-center">

            @auth
            <a class="navbar-brand" href="{{ url('/home') }}" class="flex items-center">
                <x-logo/>
            </a>
            @else
            <a class="navbar-brand" href="{{ url('/') }}" class="flex items-center">
                <x-logo/>
            </a>
            @endauth
        </div>
        <div class="hidden justify-between items-center w-full lg:flex lg:w-auto lg:order-1" id="mobile-menu-2">
            <ul class="flex flex-col font-medium lg:flex-row lg:space-x-8">
                @auth
                @if (Auth::user()->isActionleader())
                <li>
                    <a href="/admin" type="button"
                        class="flex items-center p-2 w-full text-base font-medium text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                        <span class="flex-1 ml-3 text-left whitespace-nowrap">
                            Dashboard
                        </span>
                    </a>
                </li>
                @endif
                @endauth
            </ul>
        </div>
        <x-right-navbar />
    </div>
</nav>