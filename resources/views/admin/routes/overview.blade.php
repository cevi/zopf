@extends('layouts.admin')
@section('content')

<x-page-title :title="$title" :help="$help" />
<section>
    <div class="container-fluid">
        <!-- Page Header-->
        <header>
            Total Anzahl Zöpfe: {{$route->zopf_count()}} <br>
            Routen Art: {{$routetype ? $routetype['name'] : ''}} <br>
            @if ($route->route_status['id'] > config('status.route_offen'))
                <a type="button" class="focus:outline-none text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-center text-sm px-3 py-2 mb-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900" target="_blank"
                href="{{route('routes.downloadPDF', $route->id)}}">Download PDF</a>
            @endif

            @if ($route->route_status['id'] === config('status.route_unterwegs'))
                <a type="button" class="ml-4 focus:outline-none text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-center text-sm px-3 py-2 mb-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900"
                href="{{route('routes.deliverAll', $route)}}">Alle Bestellungen abschliessen</a>
            @endif
        </header>
        @if($route->route_status['id'] === config('status.route_geplant'))
            {!! Form::model($route, ['method' => 'POST', 'action'=>['AdminRoutesController@send',$route->id]]) !!}

            <div class="form-group">
                {!! Form::submit('Vorbereitet', ['class' =>  'text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800'])!!}
            </div>
            {!! Form::close()!!}
        @endif
        @if($route->route_status['id'] === config('status.route_vorbereitet'))
            {!! Form::model($route, ['method' => 'POST', 'action'=>['AdminRoutesController@send',$route->id]]) !!}

            <div class="form-group">
                {!! Form::submit('Lossenden', ['class' =>  'text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800'])!!}
            </div>
            {!! Form::close()!!}
        @endif
        <div class="row">
            <div class="col-xl-6">
                @if ($orders)
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Vorname</th>
                            <th scope="col">Strasse</th>
                            <th scope="col">PLZ</th>
                            <th scope="col">Ort</th>
                            <th scope="col">Anzahl</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    @foreach ($orders as $order)
                    <tbody>
                        <tr>
                            <td><a href="{{route('orders.edit',$order)}}" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">{{$order->address['name']}}</a></td>
                            <td>{{$order->address['firstname']}}</td>
                            <td>{{$order->address['street']}}</td>
                            <td>{{$order->address['plz']}}</td>
                            <td>{{$order->address['city']}}</td>
                            <td>{{$order['quantity']}}</td>
                            <td>{{$order->order_status['name']}}</td>
                        </tr>
                    </tbody>
                    @endforeach
                </table>

                @endif
            </div>
            <div class="col-xl-6">
                <div style="height:630px" id="map-canvas" class="text-gray-900"></div>
            </div>

        </div>
    </div>
</section>
@endsection

@push('scripts')
@include('includes.google-maps')
<script type="module">
    var route_type = @json($route->route_type);
        var travel_mode = 'DRIVING';
        if (route_type) {
            travel_mode = route_type['travel_mode'];
        }
        setMapsArguments(@json($orders), @json($key), @json($center), travel_mode, false);
        window.onload = loadScript;
</script>
@endpush