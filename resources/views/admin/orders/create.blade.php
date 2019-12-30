@extends('layouts.admin')
@section('content')
<div class="breadcrumb-holder">
        <div class="container-fluid">
            <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/admin/orders">Bestellungen</a></li>
            <li class="breadcrumb-item active">Erfassen</li>
            </ul>
            </ul>
        </div>
    </div>
    <section>
        <div class="container-fluid">
            <!-- Page Header-->
            <header> 
                <h1 class="h3 display">Bestellungen erfassen</h1>
            </header>
            <div class="row">
                <div class="col-sm-6">
                    @include('includes.form_error')
                    {!! Form::open(['method' => 'POST', 'action'=>'AdminOrdersController@store']) !!}
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            {!! Form::label('firstname', 'Vorname:') !!}
                            {!! Form::text('firstname', null, ['class' => 'form-control']) !!}
                        </div>
                        <div class="form-group col-md-6">
                            {!! Form::label('name', 'Name:') !!}
                            {!! Form::text('name', null, ['class' => 'form-control']) !!}
                        </div>
                        {!! Form::hidden('address_id', null, ['class' => 'form-control']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('street', 'Strasse:') !!}
                        {!! Form::text('street', null, ['class' => 'form-control ']) !!}
                    </div>

                    <div class="form-row">
                            <div class="form-group col-md-3">  
                            {!! Form::label('plz', 'PLZ:') !!}
                            {!! Form::text('plz', null, ['class' => 'form-control']) !!}
                        </div>
                        <div class="form-group col-md-9">
                            {!! Form::label('city', 'Ort:') !!}
                            {!! Form::text('city', null, ['class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('quantity', 'Anzahl:') !!}
                        {!! Form::text('quantity', null, ['class' => 'form-control']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('routes_id', 'Route:') !!}
                        {!! Form::select('routes_id', [''=>'Wähle Route'] + $routes, null, ['class' => 'form-control']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::submit('Adresse Erstellen', ['class' => 'btn btn-primary'])!!}
                    </div>
                {!! Form::close()!!}
            </div>
        </div>
    </section>
@endsection


{{-- @section('scripts')
    <script type="text/javascript">

    //autocomplete script
    $(document).on('focus','.autocomplete_txt',function(){
    type = $(this).attr('name');

    $(this).autocomplete({
        minLength: 2,
        highlight: true,
        source: function( request, response ) {
                $.ajax({
                    url: "{{ route('searchajaxaddress') }}",
                    dataType: "json",
                    data: {
                        term : request.term
                    },
                    success: function(data) {
                        var array = $.map(data, function (item) {
                        return {
                            label: function(item) {
                             if(item['name']!=undefined){
                                return item['firstname'] + ' ' + item['name'] + ', ' + item['street'] + ', ' + item['city_plz'] + ' ' + item['city_name']
                                } else{
                                    return "Nichts gefunden";
                                }},
                            value: item['id'],
                            data : item
                        }
                    });
                        response(array)
                    }
                });
        },
        select: function( event, ui ) {
            
            var data = ui.item.data;  
            $("[name='address_name']").val(data.name);
            $("[name='address_firstname']").val(data.firstname);
            $("[name='address_id']").val(data.address_id);
            $("[name='address_street']").val(data.street);
            $("[name='address_city_name']").val(data.city_name);
            $("[name='address_city_plz']").val(data.city_plz);
        }
    });
    
    
    });
    </script>    
@endsection --}}