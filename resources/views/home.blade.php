@extends('home.layout')

@section('route_content')
@if($group && !$group->global)
@if($action && !$action->global)

<x-page-title :title="$title" :help="$help" :header="false" />
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

    <li><a href="{{route('home.routes',$route->id)}}"
            class="font-medium text-blue-600 dark:text-blue-500 hover:underline">{{$route->name}}</a></li>
    @endforeach
</ul>
@endif
@else
<h3 class="pt-20 text-3xl font-bold dark:text-white">Du hast noch keine zugeordnete Aktion, erstelle <a href="{{ route('actions.create') }}"
        class="text-blue-600 dark:text-blue-500 hover:underline">hier</a>
    eine
    Aktion.</h3>
@endif
@else
<h3 class="pt-20 text-3xl font-bold dark:text-white">Du hast noch keine zugeordnete Gruppe, erstelle <a href="{{ route('home.groups.create') }}"
        class="text-blue-600 dark:text-blue-500 hover:underline">hier</a> eine
    Gruppe.</h3>
@endif
@endsection