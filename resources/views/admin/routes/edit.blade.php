@extends('layouts.admin')

@section('content')
    <div class="breadcrumb-holder">
        <div class="container-fluid">
            <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/admin/routes">Routen</a></li>
            <li class="breadcrumb-item active">Bearbeiten</li>
            </ul>
        </div>
    </div>
    <section>
        <div class="container-fluid">
            <!-- Page Header-->
            <header> 
                <h1 class="h3 display">Routen</h1>
            </header>

            <div class="row">
    
                <div class="col-sm-6">
                    @if ($route->route_status_id == config('status.route_vorbereitet'))
                        <div class="alert alert-danger">Verantwortliche Person beachten.</div>    
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    {!! Form::model($route, ['method' => 'Patch', 'action'=>['AdminRoutesController@update',$route->id]]) !!}
                        <div class="form-group">
                            {!! Form::label('name', 'Name:') !!}
                            {!! Form::text('name', null, ['class' => 'form-control']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('user_id', 'Verantwortlicher:') !!}
                            {!! Form::select('user_id', $users, null, ['class' => 'form-control']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('route_type_id', 'Rolle:') !!}
                            {!! Form::select('route_type_id', $route_types, null, ['class' => 'form-control']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('route_status_id', 'Status:') !!}
                            {!! Form::select('route_status_id', $route_statuses, null, ['class' => 'form-control']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::submit('Update Aktion', ['class' => 'btn btn-primary'])!!}
                        </div>
                    {!! Form::close()!!}

                    {!! Form::model($route, ['method' => 'DELETE', 'action'=>['AdminRoutesController@destroy',$route->id]]) !!}
                        <div class="form-group">
                            {!! Form::submit('Route löschen', ['class' => 'btn btn-danger'])!!}
                        </div>
                    {!! Form::close()!!}
                </div> 
                <div class="col-sm-6">
                    <button id="chooseOrders" class="btn btn-info btn-sm">Bestellungen hinzufügen</button>
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
                                <td>{{$order->address['name']}}</td>
                                <td>{{$order->address['firstname']}}</td>
                                <td>{{$order->address['street']}}</td>
                                <td>{{$order->address['plz']}}</td>
                                <td>{{$order->address['city']}}</td>
                                <td>{{$order['quantity']}}</td>
                                <td>{{$order->order_status['name']}}</td>
                                <td>
                                    {!! Form::model($order, ['method' => 'patch', 'action'=>['AdminRoutesController@RemoveOrder',$order->id]]) !!}
                                        {!! Form::hidden('order_id', $order->id) !!}
                                        {!! Form::hidden('route_id', $route->id) !!}
                                        {!! Form::button('<i class="fa fa-trash"></i>', ['class' => 'btn btn-danger btn-sm rounded-0', 'title' => 'Aus Route entfernen', 'type' => 'submit'])!!}
                                    {!! Form::close() !!} 
                                </td>
                            </tr>
                        </tbody>
                    @endforeach
                    </table>
                
                @endif
                </div> 
            </div>
        </div>  
    </section> 
    <div class="modal fade" id="ajaxModel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modelHeading">Bestellungen Auswählen</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <button data-remote='{{route('routes.AssignOrders')}}' id="AssignOrders" class="btn btn-info btn-sm">Bestellungen zuweisen</button>
                    </div>
                    <form id="modal-form" method="POST" action="javascript:void(0)" style="height:800px;overflow-y:scroll">
                        @if ($open_orders)
                        <table class="table" id="AssignOrdersTable">
                            <thead>
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Vorname</th>
                                    <th scope="col">Strasse</th>
                                    <th scope="col">PLZ</th>
                                    <th scope="col">Ort</th>
                                    <th scope="col">Anzahl</th>
                                    <th scope="col" style="width:15%">Kommentar</th>
                                </tr>
                            </thead>
                        @foreach ($open_orders as $order)
                            <tbody>
                                <tr>
                                    <td><input type="checkbox"  id="{{$order['id']}}"></td>
                                    <td>{{$order->address['name']}}</td>
                                    <td>{{$order->address['firstname']}}</td>
                                    <td>{{$order->address['street']}}</td>
                                    <td>{{$order->address['plz']}}</td>
                                    <td>{{$order->address['city']}}</td>
                                    <td>{{$order['quantity']}}</td>
                                    <td>{{$order['comments']}}</td>
                                </tr>
                            </tbody>
                        @endforeach
                        </table>
                    
                    @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
 

@endsection
@section('scripts')
<script>
    $('#chooseOrders').on('click', function () { 
         $('#ajaxModel').modal('show');
    });
    $('#AssignOrders').on('click', function () { 
        
        var id = [];
        $('#AssignOrdersTable input[type=checkbox]:checked').each(function(){
            id.push(this.id);
        });
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        var url = $(this).data('remote');
        // confirm then
        $.ajax({
            url: url,
            method: 'POST',
            data:{
                id:id,
                route_id: @json($route['id'])},
            success:function(data)
            {   
                $('#ajaxModel').modal('hide');
                location.reload();
            }
        });
    });

    
    </script>
@endsection