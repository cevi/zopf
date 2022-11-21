<!-- Side Navbar -->
<nav class="side-navbar">
    <div class="side-navbar-wrapper">
        <div class="main-menu">
            <h5 class="sidenav-heading">Zopfaktion</h5>
            <ul id="side-main-menu" class="side-menu list-unstyled">
                <li><a href="/home"> <i class="fas fa-home"></i> Home</a></li>
                @if(Auth::user()->isActionleader() && Auth::user()->action)
                    <li><a href="/admin"> <i class="icon-home"></i>Dashboard</a></li>
                    <li>
                        <a href="{{route('progress.index')}}"><i class="fa-solid fa-chart-area"></i> Backstuben Verlauf</a>
                    </li>
                    <li><a href="#OrdersDropdown" aria-expanded="false" data-toggle="collapse"> <i class="icon-padnote"></i>Bestellungen</a>
                        <ul id="OrdersDropdown" class="collapse list-unstyled ">
                            <li>
                                <a href="{{route('orders.index')}}">Alle Bestellungen</a>
                            </li>

                            <li>
                                <a href="{{route('orders.create')}}">Bestellung erfassen</a>
                            </li>
                            <li>
                                <a href="{{route('orders.map')}}">Karte ansehen</a>
                            </li>
                        </ul>
                        <!-- /.nav-second-level -->
                    </li>
                    <li><a href="#RoutesDropdown" aria-expanded="false" data-toggle="collapse"> <i class="icon-paper-airplane"></i>Routen</a>
                        <ul id="RoutesDropdown" class="collapse list-unstyled ">
                            <li>
                                <a href="{{route('routes.index')}}">Alle Routen</a>
                            </li>

                            <li>
                            <a href="{{route('routes.create')}}">Route erfassen</a>
                            </li>
                            <li>
                                <a href="{{route('routes.map')}}">Karte ansehen</a>
                            </li>
                        </ul>
                            <!-- /.nav-second-level -->
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
                    <li><a href="#GroupDropdown" aria-expanded="false" data-toggle="collapse"> <i class="icon-list"></i>Gruppen</a>
                        <ul id="GroupDropdown" class="collapse list-unstyled ">
                            <li><a href="{{route('groups.index')}}">Alle Gruppen</a></li>
                        </ul>
                    </li>
                @endif
                <li><a href="#UserDropdown" aria-expanded="false" data-toggle="collapse"> <i class="icon-user"></i>Leiter</a>
                    <ul id="UserDropdown" class="collapse list-unstyled ">
                        <li>
                            <a href="{{route('users.index')}}">Alle Leiter</a>
                        </li>
                    </ul>
                    <!-- /.nav-second-level -->
                </li>
                <li><a href="#ActionDropdown" aria-expanded="false" data-toggle="collapse"> <i class="icon-form"></i> Aktionen</a>
                    <ul id="ActionDropdown" class="collapse list-unstyled ">
                        <li>
                            <a href="{{route('actions.index')}}">Alle Aktionen</a>
                        </li>
                        <li>
                        <a href="{{route('actions.create')}}">Aktion erfassen</a>
                        </li>
                    </ul>
                    <!-- /.nav-second-level -->
                </li>
            </ul>
        </div>
    </div>
</nav>

