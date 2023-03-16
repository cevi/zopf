<!-- Side Navbar -->
<nav class="side-navbar">
    <div class="side-navbar-wrapper">
        <div class="main-menu">
            <h5 class="sidenav-heading">Zopfaktion</h5>
            <ul id="side-main-menu" class="side-menu list-unstyled">
                <li>
                    <a href="/home"
                       class="flex items-center p-2 text-base font-normal text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700">
                        <i class="fa-solid fa-house-chimney"></i>
                        <span class="ml-3">Home</span>
                    </a>
                @if(Auth::user()->isActionleader() && Auth::user()->action && !Auth::user()->action->global)
                    <li>
                        <a href="/admin"
                           class="flex items-center p-2 text-base font-normal text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700">
                            <i class="fa-solid fa-house-user"></i>
                            <span class="ml-3">Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{route('progress.index')}}"
                           class="flex items-center p-2 text-base font-normal text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700">
                            <i class="fa-solid fa-chart-area"></i>
                            <span class="ml-3">Backstuben Verlauf</span>
                        </a>
                    </li>
                    <li>
                        <button type="button"
                                class="flex items-center p-2 w-full text-base font-normal text-gray-900 transition duration-75 group dark:text-white  hover:bg-gray-100 dark:hover:bg-gray-700"
                                aria-controls="dropdown-orders" data-collapse-toggle="dropdown-orders">
                            <i class="fa-solid fa-newspaper"></i>
                            <span class="flex-1 ml-3 text-left whitespace-nowrap"
                                  sidebar-toggle-item>Bestellungen</span>
                            <i class="fa-solid fa-angle-down"></i>
                        </button>
                        <ul id="dropdown-orders" class="hidden py-2 space-y-2">
                            <li>
                                <a href="{{route('orders.index')}}"
                                   class="flex items-center p-2 pl-11 w-full text-base font-normal text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">Alle
                                    Bestellungen</a>
                            </li>
                            <li>
                                <a href="{{route('orders.create')}}"
                                   class="flex items-center p-2 pl-11 w-full text-base font-normal text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">Bestellung
                                    erfassen</a>
                            </li>
                            <li>
                                <a href="{{route('orders.map')}}"
                                   class="flex items-center p-2 pl-11 w-full text-base font-normal text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">Karte
                                    ansehen</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <button type="button"
                                class="flex items-center p-2 w-full text-base font-normal text-gray-900 transition duration-75 group dark:text-white  hover:bg-gray-100 dark:hover:bg-gray-700"
                                aria-controls="dropdown-routes" data-collapse-toggle="dropdown-routes">
                            <i class="fa-solid fa-route"></i>
                            <span class="flex-1 ml-3 text-left whitespace-nowrap"
                                  sidebar-toggle-item>Routen</span>
                            <i class="fa-solid fa-angle-down"></i>
                        </button>
                        <ul id="dropdown-routes" class="hidden py-2 space-y-2">
                            <li>
                                <a href="{{route('routes.index')}}"
                                   class="flex items-center p-2 pl-11 w-full text-base font-normal text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">Alle
                                    Routen</a>
                            </li>
                            <li>
                                <a href="{{route('routes.create')}}"
                                   class="flex items-center p-2 pl-11 w-full text-base font-normal text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">Route
                                    erfassen</a>
                            </li>
                            <li>
                                <a href="{{route('routes.map')}}"
                                   class="flex items-center p-2 pl-11 w-full text-base font-normal text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">Karte
                                    ansehen</a>
                            </li>
                        </ul>
                    </li>
                @endif

                {{-- <li><a href="#AddressDropdown" aria-expanded="false" data-toggle="collapse"> <i class="icon-website"></i>Adressen</a>
                    <ul id="AddressDropdown" class="collapse list-unstyled ">
                        <li>
                            <a href="{{route('addresses.index')}}">Alle Adressen</a>
                        </li>

                        <li>
                        <a href="{{route('addresses.create')}}">Adressen erstellen</a>
                        </li>
                    </ul>
                    <!-- /.nav-second-level -->
                </li> --}}
            </ul>
        </div>
        <div class="admin-menu">
            <h5 class="sidenav-heading">Administration</h5>
            <ul id="side-main-menu" class="side-menu list-unstyled">
                @if (Auth::user()->isAdmin())
                    <li>
                        <a href="{{route('groups.index')}}"
                           class="flex items-center p-2 text-base font-normal text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700">
                            <i class="fa-solid fa-people-group"></i>
                            <span class="ml-3">Gruppen</span>
                        </a>
                    </li>
                @endif
                <li>
                    <a href="{{route('users.index')}}"
                       class="flex items-center p-2 text-base font-normal text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700">
                        <i class="fa-regular fa-user"></i>
                        <span class="ml-3">Leiter</span>
                    </a>
                </li>
                <li>
                    <button type="button"
                            class="flex items-center p-2 w-full text-base font-normal text-gray-900 transition duration-75 group dark:text-white  hover:bg-gray-100 dark:hover:bg-gray-700"
                            aria-controls="dropdown-actions" data-collapse-toggle="dropdown-actions">
                        <i class="fa-regular fa-folder-open"></i>
                        <span class="flex-1 ml-3 text-left whitespace-nowrap"
                              sidebar-toggle-item>Aktionen</span>
                        <i class="fa-solid fa-angle-down"></i>
                    </button>
                    <ul id="dropdown-actions" class="hidden py-2 space-y-2">
                        <li>
                            <a href="{{route('actions.index')}}"
                               class="flex items-center p-2 pl-11 w-full text-base font-normal text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">Alle
                                Aktionen</a>
                        </li>
                        <li>
                            <a href="{{route('actions.create')}}"
                               class="flex items-center p-2 pl-11 w-full text-base font-normal text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">Aktion
                                erfassen</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="/admin/changes"
                       class="flex items-center p-2 text-base font-normal text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700">
                        <i class="fas fa-clipboard-list"></i>
                        <span class="ml-3">Rückmeldungen / Änderungen</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

