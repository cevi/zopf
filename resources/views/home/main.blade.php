@extends('home.layout')

@section('route_content')
    <h1>{{$route->name}}</h1> <br>

    <div>
        <a type="button" class="btn active btn-info btn" href="#">Liste</a>
        <a type="button" class="btn btn-info btn" href="{{route('home.maps',$route->id)}}">Karte</a>
    </div>                 
    <!-- Author -->
    @if ($orders)
        @if($orders->min('order_status_id')<  config('status.order_ausgeliefert'))
            <div id="aspect-content">
                @foreach ($orders as $key=>$order)
                <div class="aspect-tab ">
                    <input id="item-{{$key}}" type="checkbox" class="aspect-input" name="aspect">
                    <label for="item-{{$key}}" class="aspect-label"></label>
                    <div class="aspect-content">
                        <div class="aspect-info">
                            <div class="chart-pie">
                                <span class="chart-pie-count">{{$order['sequence']+1}}</span>
                            </div>
                            
                            <span class="aspect-name">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <span>{{$order->address['firstname']}} {{$order->address['name']}}</span>
                                    </div>
                                    <div class="col-sm-6">
                                        <span>{{$order->address['street']}} <br> {{$order->address['plz']}} {{$order->address['city']}}</span>
                                    </div>
                                </div>
                            </span>
                        </div>
                        <div class="aspect-stat">
                            <div class="all-opinions">
                                <span>{{$order->quantity==1? $order->quantity." Zopf" :  $order->quantity." Zöpfe"}}</span>
                            </div>
                            <div>
                                <span class="positive-count">
                                    @if($order['order_status_id'] === config('status.order_hinterlegt'))
                                        Hinterlegt
                                    @elseif ($order['order_status_id'] === config('status.order_ausgeliefert'))
                                        Übergeben
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="aspect-tab-content">
                        <div class="sentiment-wrapper">
                            <div>
                                <div>
                                    <div class="opinion-header">
                                        <span>Adressinformationen</span>
                                    </div>
                                    <div>
                                        <span>
                                            <table class="table table-sm">
                                                <tr>
                                                    <td>Name: </td>
                                                    <td>{{$order->address['firstname']}} {{$order->address['name']}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Strasse: </td>
                                                    <td>{{$order->address['street']}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Ortschaft: </td>
                                                    <td>{{$order->address['plz']}} {{$order->address['city']}}</td>
                                                </tr>
                                            </table>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div>
                                    <div class="opinion-header">
                                        <span>Anzahl</span>
                                        <span>{{$order['quantity']}}</span>
                                    </div>
                                    <div>
                                        <span>     
                                            @if($order['order_status_id'] === config('status.order_hinterlegt'))
                                                <td>Hinterlegt</td>
                                            @elseif ($order['order_status_id'] === config('status.order_ausgeliefert'))
                                                <td>Übergeben</td>
                                            @else
                                                <td><a type="button" class="btn btn-info btn-sm" href="{{route('home.delivered', $order->id)}}">Übergeben</a>
                                                    <a type="button" class="btn btn-info btn-sm" href="{{route('home.deposited', $order->id)}}">Hinterlegt</a></td>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div>
                                    <div class="opinion-header">
                                        <span>Bemerkungen</span>
                                    </div>
                                    <div>
                                        <span>{{$order['comments']}}</span>
                                    </div>
                                </div>
                            </div>
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
    {{-- @if ($orders)
        @if($orders->min('order_status_id')<  config('status.order_ausgeliefert'))
                                
            <div class="panel-group" id="accordion"> <!-- accordion 1 -->
                @foreach ($orders as $key=>$order)
                    <div class="panel panel-primary">
                        <div class="panel-heading"> <!-- panel-heading -->
                            <h4 class="panel-title"> <!-- title 1 -->
                            <a data-toggle="collapse" data-parent="#accordion" href="#accordion{{$order->id}}">
                                {{$order['sequence']+1}}. {{$order->address['firstname']}} {{$order->address['name']}}
                                
                                @if($order['order_status_id'] === config('status.order_hinterlegt'))
                                (Hinterlegt)
                            @elseif ($order['order_status_id'] === config('status.order_ausgeliefert'))
                                (Übergeben)
                            @endif
                            </a>
                        </h4>
                        </div>
                        <!-- panel body -->
                        <div id="accordion{{$order->id}}" class="panel-collapse collapse in">
                            <div class="panel-body">
                                <table class="table table-sm">
                                    <tr>
                                        <td>Name: </td>
                                        <td>{{$order->address['firstname']}} {{$order->address['name']}}</td>
                                    </tr>
                                    <tr>
                                        <td>Strasse: </td>
                                        <td>{{$order->address['street']}}</td>
                                    </tr>
                                    <tr>
                                        <td>Ortschaft: </td>
                                        <td>{{$order->address['plz']}} {{$order->address['city']}}</td>
                                    </tr>
                                    <tr>
                                        <td>Anzahl: </td>
                                        <td>{{$order['quantity']}}</td>
                                    </tr>
                                    <tr>
                                        <td>Bemerkung: </td>
                                        <td>{{$order['comments']}}</td>
                                    </tr>
                                    <tr>
                                        <td>Aktion: </td>
                                        @if($order['order_status_id'] === config('status.order_hinterlegt'))
                                            <td>Hinterlegt</td>
                                        @elseif ($order['order_status_id'] === config('status.order_ausgeliefert'))
                                            <td>Übergeben</td>
                                        @else
                                            <td><a type="button" class="btn btn-info btn-sm" href="{{route('home.delivered', $order->id)}}">Übergeben</a>
                                                <a type="button" class="btn btn-info btn-sm" href="{{route('home.deposited', $order->id)}}">Hinterlegt</a></td>
                                        @endif
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            
        @else
         Alle Bestellungen ausgeliefert. 
         Komme zurück zur Zentrale.   
        @endif
    @endif --}}
        
@endsection
