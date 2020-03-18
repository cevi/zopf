@extends('home.layout')

@section('route_content')
    <h1>{{$route->name}}</h1> <br>

    <div>
    <a type="button" class="btn active btn-info btn" href="#">Liste</a>
    <a type="button" class="btn btn-info btn" href="{{route('home.maps',$route->id)}}">Karte</a>
    </div> <br>                     
    <!-- Author -->
    @if ($orders)
        @if($orders->min('order_status_id')<20)
                                
            <div class="panel-group" id="accordion"> <!-- accordion 1 -->
                @foreach ($orders as $order)
                    <div class="panel panel-primary">
                        <div class="panel-heading"> <!-- panel-heading -->
                            <h4 class="panel-title"> <!-- title 1 -->
                            <a data-toggle="collapse" data-parent="#accordion" href="#accordion{{$order->id}}">
                                {{$order->address['firstname']}} {{$order->address['name']}}
                                
                                @if($order['order_status_id']===25)
                                (Hinterlegt)
                            @elseif ($order['order_status_id']===20)
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
                                        @if($order['order_status_id']===25)
                                            <td>Hinterlegt</td>
                                        @elseif ($order['order_status_id']===20)
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
    @endif
        
@endsection
