<div class="navbar-default sidebar" role="navigation">
    <div class="sidebar-nav navbar-collapse">
        <ul class="nav" id="side-menu">
            <li>
                <a href="/admin"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
            </li>
            @if (Auth::user()->isAdmin())
                <li>
                    <a href="#"><i class="fa fa-users fa-fw"></i> Gruppen<span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li>
                            <a href="{{route('groups.index')}}">Alle Gruppen</a>
                        </li>  
                    </ul>
                    <!-- /.nav-second-level -->
                </li>
            @endif
            <li>
                <a href="#"><i class="fa fa-user fa-fw"></i> Leiter<span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li>
                        <a href="{{route('users.index')}}">Alle Leiter</a>
                    </li>

                    <li>
                    <a href="{{route('users.create')}}">Leiter erstellen</a>
                    </li>
                </ul>
                <!-- /.nav-second-level -->
            </li>
            <li>
                <a href="#"><i class="fa fa-bar-chart-o fa-fw"></i> Aktion<span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li>
                        <a href="{{route('actions.index')}}">Alle Aktionen</a>
                    </li>

                    <li>
                    <a href="{{route('actions.create')}}">Aktion erstellen</a>
                    </li>
                </ul>
                <!-- /.nav-second-level -->
            </li>
            <li>
                <a href="#"><i class="fa fa-map fa-fw"></i> Routen<span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li>
                        <a href="{{route('routes.index')}}">Alle Routen</a>
                    </li>

                    <li>
                    <a href="{{route('routes.create')}}">Route erstellen</a>
                    </li>
                </ul>
                <!-- /.nav-second-level -->
            </li>
            <li>
                <a href="#"><i class="fa fa-list-alt fa-fw"></i>Bestellungen<span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li>
                        <a href="{{route('orders.index')}}">Alle Bestellungen</a>
                    </li>

                    <li>
                    <a href="{{route('orders.create')}}">Bestellung erstellen</a>
                    </li>
                </ul>
                <!-- /.nav-second-level -->
            </li>
            <li>
                <a href="#"><i class="fa fa-home fa-fw"></i>Adressen<span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li>
                        <a href="{{route('addresses.index')}}">Alle Adressen</a>
                    </li>

                    <li>
                    <a href="{{route('addresses.create')}}">Adressen erstellen</a>
                    </li>
                </ul>
                <!-- /.nav-second-level -->
            </li>
        </ul>
    </div>
    <!-- /.sidebar-collapse -->
</div>