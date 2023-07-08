@extends('home.layout')

@section('route_content')
    <x-page-title :title="$title" :help="$help" :header="false"/>

    <div>
        <a type="button" class="btn-primary active btn-sm" href="{{route('home.routes',$route->id)}}">Liste</a>
        <a type="button" class="btn-primary active btn-sm" href="#">Karte</a>
    </div> <br>
    <!-- Author -->
    @if ($orders)

        <div class="col-sm-9">
            <div style="height: 600px" id="map-canvas"></div>
        </div>
    @endif

@endsection
@section('scripts')
    @include('includes.google-maps')
    <script>
        setMapsArguments(@json($orders), @json($key), @json($center), @json($route->route_type['travelmode']), false);
        window.onload = loadScript;
    </script>
@endsection
