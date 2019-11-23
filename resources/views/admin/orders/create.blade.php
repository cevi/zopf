@extends('layouts.admin')
@section('content')
<h1>Erstelle Bestellung</h1>


    <div class="col-sm-4">
        @include('includes.form_error')
        {!! Form::open(['method' => 'POST', 'action'=>'AdminOrdersController@store']) !!}
        <div class="col-sm-6" style="padding-left: 0px;">
            <div class="form-group">
                {!! Form::label('address_name', 'Name:') !!}
                {!! Form::text('address_name', null, ['class' => 'form-control autocomplete_txt']) !!}
            </div>
        </div>
        <div class="col-sm-6" style="padding-right: 0px;">
            <div class="form-group">
                {!! Form::label('address_firstname', 'Vorname:') !!}
                {!! Form::text('address_firstname', null, ['class' => 'form-control autocomplete_txt']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::submit('Adresse Erstellen', ['class' => 'btn btn-primary'])!!}
        </div>
        {!! Form::close()!!}
    </div>
   @endsection


    @section('scripts')
        <script type="text/javascript">

        //autocomplete script
        $(document).on('focus','.autocomplete_txt',function(){
        type = $(this).attr('name');

        if(type =='address_name' )autoType='name'; 
        if(type =='address_firstname' )autoType='firstname'; 

        $(this).autocomplete({
            minLength: 2,
            source: function( request, response ) {
                    $.ajax({
                        url: "{{ route('searchajaxaddress') }}",
                        dataType: "json",
                        data: {
                            term : request.term,
                            type : type,
                        },
                        success: function(data) {
                            var array = $.map(data, function (item) {
                            return {
                                label: item[autoType],
                                value: item[autoType],
                                data : item
                            }
                        });
                            response(array)
                        }
                    });
            },
            select: function( event, ui ) {
                var data = ui.item.data;           
                $('#address_name').val(data.name);
                $('#address_firstname').val(data.firstname);
            }
        });
        
        
        });
        </script>    
    @endsection