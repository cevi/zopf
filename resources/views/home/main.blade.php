@extends('home.layout')

@section('route_content')
    <h1>{{$route->name}}</h1> <br>

    <div>
        <a type="button" class="btn active btn-info btn" href="#">Liste</a>
        <a type="button" class="btn btn-info btn" href="{{route('home.maps',$route->id)}}">Karte</a>
    </div>
    <br>
    <!-- Author -->
    @if ($orders)
        @if($orders->min('order_status_id')<  config('status.order_ausgeliefert'))
            <div id="accordion-flush" data-accordion="collapse">
                @foreach ($orders as $key=>$order)
                    <h2 id="accordion-flush-heading-{{$key}}" class="mobile-accordion">
                        <button type="button"
                                class="row flex items-center justify-between w-full font-medium text-left text-gray-500 border border-b-0 border-gray-200 {{$key === 0 ? 'rounded-t-xl' : ''}} focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-800 dark:border-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800"
                                data-accordion-target="#accordion-collapse-body-{{$key}}" aria-expanded="false"
                                aria-controls="accordion-collapse-body-{{$key}}">
                                <span class="col-sm-2">
                                    <span class="font-bold">{{$order['sequence']+1}}</span>
                                </span>
                            <span class="col-sm-4">
                                    <span>{{$order->address['firstname']}} {{$order->address['name']}}</span>
                                </span>
                            <span class="col-sm-3">
                                    <span>{{$order->address['street']}} <br> {{$order->address['plz']}} {{$order->address['city']}}</span>
                                </span>
                            <span class="col-sm-3">
                                <span>{{$order->quantity==1? $order->quantity." Zopf" :  $order->quantity." Zöpfe"}}
                                    @if($order['order_status_id'] === config('status.order_hinterlegt'))
                                        Hinterlegt
                                    @elseif ($order['order_status_id'] === config('status.order_ausgeliefert'))
                                        Übergeben
                                    @endif
                                </span>
                            </span>
                        </button>
                    </h2>
                    <div id="accordion-collapse-body-{{$key}}" class="hidden"
                         aria-labelledby="accordion-collapse-heading-{{$key}}">
                        <div
                            class="row accordion-content font-medium border-b border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-800">
                            <div class="col-sm-4">
                                <div class="opinion-header">
                                    <span>Adressinformationen</span>
                                </div>
                                <div>
                                    <table class="table">
                                        <tr>
                                            <td>Name:</td>
                                            <td>{{$order->address['firstname']}} {{$order->address['name']}}</td>
                                        </tr>
                                        <tr>
                                            <td>Strasse:</td>
                                            <td>{{$order->address['street']}}</td>
                                        </tr>
                                        <tr>
                                            <td>Ortschaft:</td>
                                            <td>{{$order->address['plz']}} {{$order->address['city']}}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <br>
                            <div class="col-sm-4">
                                <div class="opinion-header">
                                    <span>Bemerkungen</span>
                                </div>
                                <div>
                                    <span>{{$order['comments']}}</span>
                                </div>
                                <br>
                            </div>
                            <div class="col-sm-4">
                                <div class="opinion-header">
                                    <span>Anzahl</span>
                                    <span>{{$order['quantity']}}</span>
                                </div>
                                <br>
                                <div>
                                    <span>
                                        @if($order['order_status_id'] === config('status.order_hinterlegt'))
                                            <td>Hinterlegt</td>
                                        @elseif ($order['order_status_id'] === config('status.order_ausgeliefert'))
                                            <td>Übergeben</td>
                                        @else
                                            <td><a type="button" class="btn btn-info"
                                                   href="{{route('home.delivered', $order->id)}}">Übergeben</a>
                                                <a type="button" class="btn btn-info"
                                                   href="{{route('home.deposited', $order->id)}}">Hinterlegt</a></td>
                                        @endif
                                    </span>
                                </div>
                                <br>
                            </div>
                        </div>
                    </div>

                @endforeach
            </div>

        @else
            Alle Bestellungen ausgeliefert.
            Komme zurück zur Zentrale.
        @endif
    @endif
@endsection
