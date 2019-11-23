@extends('layouts.admin')

@section('content')

    <h1>Bestellungen</h1> 
    <div class="col-sm-9">
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
                        <th scope="col">Route</th>
                        <th scope="col">Status</th>
                        <th scope="col">Created Date</th>
                        <th scope="col">Updated Date</th>
                    </tr>
                </thead>
            @foreach ($orders as $order)
                <tbody>
                    <tr>
                        <td><a href="{{route('orders.edit',$order->id)}}">{{$order->adress['name']}}</a></td>
                        <td>{{$order->adress['firstname']}}</a></td>
                        <td>{{$order->adress['stree']}}</a></td>
                        <td>{{$order->adress->city['plz']}}</a></td>
                        <td>{{$order->adress->city['name']}}</a></td>
                        <td>{{$order->quantity}}</a></td>
                        <td>{{$order->route['name']}}</a></td>
                        <td>{{$order->order_status['name']}}</a></td>
                        <td>{{$order->created_at ? $order->created_at->diffForHumans() : 'no date'}}</td>
                        <td>{{$order->updated_at ? $order->updated_at->diffForHumans() : 'no date'}}</td>
                    </tr>
                </tbody>
            @endforeach
            </table>
        
        @endif

    </div>
@endsection