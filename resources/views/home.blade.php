@extends('home.layout')

@section('route_content')
    @if($group && !$group->global)
        @if($action && !$action->global)
            <h1>{{$action->name}} {{$action->year}}</h1>

            <!-- Author -->
            <p class="lead">
                @if(!$routes->isEmpty())
                    Hallo {{$user->username}}, du hast folgende offene Routen:
                @else
                    Hallo {{$user->username}}, du hast keine offene Routen.
                @endif
            </p>
            @if ($routes)

                <ul class="list-unstyled">
                    @foreach ($routes as $route)

                    <li><a href="{{route('home.routes',$route->id)}}">{{$route->name}}</a></li>
                    @endforeach
                </ul>
            @endif
        @else
            <h3>Du hast noch keine zugeordnete Aktion, erstelle <a href="{{ route('actions.create') }}">hier</a> eine Aktion.</h3>
        @endif
    @else
        <h3>Du hast noch keine zugeordnete Gruppe, erstelle <a href="{{ route('home.groups.create') }}">hier</a> eine Gruppe.</h3>
    @endif
@endsection
