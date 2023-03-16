<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Zopfaktion">
    <meta name="author" content="Jérôme Sigg">
    <meta name="robots" content="all,follow">
    <title>Zopfaktion Zentrale</title>
    <!-- Bootstrap Core CSS -->
    <link href="{{asset('css/libs.css')}}" rel="stylesheet">
    @yield('styles')
</head>
<body>
<section>
    <div class="container-fluid" style="page-break-inside:avoid; margin-bottom:50px">
        <!-- Page Header-->
        <header>
            <h1 class="h3 display">Übersicht {{$route['name']}}</h1>
            Total Anzahl Zöpfe: {{$orders->sum('quantity')}} <br>
            Routen Art: {{$routetype['name']}} <br>
        </header>
        <div class="row">
            @if ($orders)
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col" width="20%">Name</th>
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
    <div class="container-fluid">
        <div class="row">
            <img src="{{asset("storage/".$save_path)}}" alt="Route"/>
        </div>
    </div>
</section>
<!-- jQuery -->
<script src="{{asset('js/libs.js')}}"></script>
</body>
</html>
