@extends('layouts.admin')
@section('content')
<div class="breadcrumb-holder">
        <div class="container-fluid">
            <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/admin/routes">Routen</a></li>
            <li class="breadcrumb-item active">Übersicht</li>
            </ul>
            </ul>
        </div>
    </div>
    <section>
        <div class="container-fluid">
            <!-- Page Header-->
            <header> 
                <h1 class="h3 display">Übersicht Routen</h1>
            </header>
            <div class="row">
                <div class="col-sm-6">
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
                                <td>{{$order->address['name']}}</a></td>
                                <td>{{$order->address['firstname']}}</a></td>
                                <td>{{$order->address['street']}}</a></td>
                                <td>{{$order->address['plz']}}</a></td>
                                <td>{{$order->address['city']}}</a></td>
                                <td>{{$order['quantity']}}</a></td>
                                <td>{{$order->order_status['name']}}</a></td>
                            </tr>
                        </tbody>
                    @endforeach
                    </table>
                
                @endif
                </div>
                    
            </div>
        </div>
    </section>
@endsection