<nav
    class="dark:bg-gray-800 border-b bg-gray-100 border-gray-200 px-4 xl:px-6 py-2.5 dark:border-gray-700 fixed left-0 right-0 top-0 z-50">
    <div class="flex flex-wrap justify-between items-center">
        <div class="flex justify-start items-center">
            <a href="{{ url('/admin') }}" class="flex items-center justify-between">
                <x-logo/>
            </a>
        </div>
        <div class="hidden justify-between items-center w-full lg:flex lg:w-auto lg:order-1" id="mobile-menu-2">
            <ul class="flex flex-col font-medium lg:flex-row xl:space-x-8">
                <li>
                    <a href="/home" type="button"
                        class="flex items-center p-2 w-full text-navbar font-medium text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                        <i class="fas fa-home"></i>
                        <span class="flex-1 ml-3 text-left whitespace-nowrap">
                            Home
                        </span>
                    </a>
                </li>
                <li>
                    <a href="/admin" type="button"
                        class="flex items-center p-2 w-full text-navbar font-medium text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                        <i class="fas fa-home"></i>
                        <span class="flex-1 ml-3 text-left whitespace-nowrap">
                            Dashboard
                        </span>
                    </a>
                </li>
                @if(Auth::user()->isActionleader() && Auth::user()->action && !Auth::user()->action->global)
                <li>
                    <a href="{{route('orders.index')}}" type="button"
                        class="flex items-center p-2 w-full text-navbar font-medium text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                        <i class="fa-solid fa-newspaper"></i>
                        <span class="flex-1 ml-3 text-left whitespace-nowrap">
                            Bestellungen
                        </span>
                    </a>
                </li>
                <li>
                    <a href="{{route('routes.index')}}" type="button"
                        class="flex items-center p-2 w-full text-navbar font-medium text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                        <i class="fa-solid fa-route"></i>
                        <span class="flex-1 ml-3 text-left whitespace-nowrap">
                            Routen
                        </span>
                    </a>
                </li>
                <li>
                    <button type="button"
                        class="flex items-center p-2 w-full text-navbar font-medium text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
                        id="maps-menu-button" aria-expanded="false" data-dropdown-toggle="dropdown-maps">
                        <span class="flex-1 ml-3 text-left whitespace-nowrap">
                            Karten
                        </span>
                        <svg aria-hidden="true" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                clip-rule="evenodd">
                            </path>
                        </svg>
                    </button>
                </li>
                <div class="hidden z-50 my-4 w-80 text-navbar list-none navbar-background divide-y divide-gray-100 shadow dark:bg-gray-700 dark:divide-gray-600 rounded-xl"
                    id="dropdown-maps">
                    <ul aria-labelledby="dropdown-maps" class="py-1 text-gray-700 dark:text-gray-300">
                        <li>
                            <a href="{{route('orders.map')}}"
                                class="block py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white">
                                <i class="fa-solid fa-newspaper"></i>
                                <span class="flex-1 ml-3 text-left whitespace-nowrap">
                                    Bestellungen
                                </span>
                            </a>
                        </li>
                        <li>
                            <a href="{{route('routes.map')}}"
                                class="block py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white">
                                <i class="fa-solid fa-route"></i>
                                <span class="flex-1 ml-3 text-left whitespace-nowrap">
                                    Routen
                                </span>
                            </a>
                        </li>
                    </ul>
                </div>
                <li>
                    <button type="button"
                        class="flex items-center p-2 w-full text-navbar font-medium text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
                        id="dashboard-menu-button" aria-expanded="false" data-dropdown-toggle="dropdown-dashboard">
                        <span class="flex-1 ml-3 text-left whitespace-nowrap">
                            Aktions-Administration
                        </span>
                        <svg aria-hidden="true" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                clip-rule="evenodd">
                            </path>
                        </svg>
                    </button>
                </li>
                <div class="hidden z-50 my-4 w-80 text-navbar list-none navbar-background rounded divide-y divide-gray-100 shadow dark:bg-gray-700 dark:divide-gray-600 rounded-xl"
                    id="dropdown-dashboard">
                    <ul aria-labelledby="dropdown-dashboard" class="py-1 text-gray-700 dark:text-gray-300">
                        <li>
                            <a href="{{route('progress.index')}}"
                                class="block py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white">
                                <i class="fa-solid fa-chart-area"></i>
                                <span class="flex-1 ml-3 text-left whitespace-nowrap">
                                    Backstuben Verlauf
                                </span>
                            </a>
                        </li>
                        <li>
                            <a href="{{route('users.index')}}"
                                class="block py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white">
                                <i class="fa-regular fa-user"></i>
                                <span class="flex-1 ml-3 text-left whitespace-nowrap">
                                    Leitende
                                </span>
                            </a>
                        </li>
                        <li>
                            <a class="block py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white"
                                href="{{route('actions.index')}}">
                                <i class="fa-regular fa-folder-open"></i>
                                <span class="flex-1 ml-3 text-left whitespace-nowrap">
                                    Aktionen
                                </span>
                            </a>
                        </li>
                        <li>
                            <a class="block py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white"
                                href="{{route('groups.index')}}">
                                <i class="fa-solid fa-people-group"></i>
                                <span class="flex-1 ml-3 text-left whitespace-nowrap">
                                    Gruppen
                                </span>
                            </a>
                        </li>
                        @if (Auth::user()->isAdmin())
                        <li>
                            <a class="block py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white"
                                href="{{route('helps.index')}}">
                                <i class="fa-solid fa-question"></i>
                                <span class="flex-1 ml-3 text-left whitespace-nowrap">
                                    Hilfe-Artikel
                                </span>
                            </a>
                        <li>
                            <a class="block py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white"
                                href="/admin/feedback">
                                <i class="fas fa-clipboard-list"></i>
                                <span class="flex-1 ml-3 text-left whitespace-nowrap">
                                    Feedbacks
                                </span>
                            </a>
                        </li>
                        </li>
                        @endif
                        <li>
                            <a href="/admin/changes"
                                class="block py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white">
                                <i class="fas fa-clipboard-list"></i>
                                <span class="ml-3">Rückmeldungen / Änderungen</span>
                            </a>
                        </li>
                    </ul>
                </div>
                @endif
            </ul>
        </div>
        <x-right-navbar />
    </div>
</nav>