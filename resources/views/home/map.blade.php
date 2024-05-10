@extends('home.layout')

@section('route_content')
<x-page-title :title="$title" :help="$help" :header="false" />

<div>
    <a type="button"
        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"
        href="{{route('home.routes',$route->id)}}">Liste</a>
    <a type="button"
        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"
        href="#">Karte</a>
</div>
<br>

<!-- Author -->
@if ($orders)

<div class="col-sm-9">
    <div style="height: 600px" id="map-canvas" class="text-gray-900"></div>
</div>
@endif

@endsection
@push('scripts')
@include('includes.google-maps')
<script type="module">
    setMapsArguments(@json($orders), @json($key), @json($center), @json($route->route_type['travelmode']), false);
        window.onload = loadScript;
</script>
@endpush