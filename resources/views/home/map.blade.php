
@extends('home.layout')

@section('route_content')
    <h1>{{$route->name}}</h1> <br>

    <div>
    <a type="button" class="btn active btn-info btn" href="{{route('home.routes',$route->id)}}">Liste</a>
    <a type="button" class="btn btn-info btn" href="#">Karte</a>
    </div> <br>                     
    <!-- Author -->
    @if ($orders)
                                
        <div class="col-sm-9">
            <div style="height: 600px" id="map-canvas"></div>
        </div>
    @endif
        
@endsection
@section('scripts')
    <script>
        setMapsArguments(@json($orders), @json($key), @json($center), @json($route->route_type['travelmode']), false);
        window.onload = loadScript;
    </script>
@endsection