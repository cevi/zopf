@extends('home.layout')

@section('route_content')
    @if($action)
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
    @endif
@endsection
