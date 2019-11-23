@extends('layouts.admin')

@section('content')

    <h1>Adresse</h1>
    <div class="col-sm-6">
        {!! Form::model($address, ['method' => 'Patch', 'action'=>['AdminAddressesController@update',$address->id]]) !!}
        <div class="col-sm-6" style="padding-left: 0px;">
                <div class="form-group">
                    {!! Form::label('name', 'Name:') !!}
                    {!! Form::text('name', null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="col-sm-6" style="padding-right: 0px;">
                <div class="form-group">
                    {!! Form::label('firstname', 'Vorname:') !!}
                    {!! Form::text('firstname', null, ['class' => 'form-control']) !!}
                </div>
            </div>
    
            <div class="form-group">
                {!! Form::label('street', 'Strasse:') !!}
                {!! Form::text('street', null, ['class' => 'form-control']) !!}
            </div>
    
            
            <div class="col-sm-3" style="padding-left: 0px;">
                <div class="form-group">    
                    {!! Form::label('city_plz', 'PLZ:') !!}
                    {!! Form::text('city_plz', $city_plz, ['class' => 'form-control autocomplete_txt']) !!}
                </div>
            </div>
            <div class="col-sm-9" style="padding-right: 0px;">
                <div class="form-group"> 
                    {!! Form::label('city_name', 'Ort:') !!}
                    {!! Form::text('city_name', $city_name, ['class' => 'form-control autocomplete_txt']) !!}
                </div>
            </div>
    
            @if (Auth::user()->isAdmin())
                <div class="form-group">
                    {!! Form::label('group_id', 'Gruppe:') !!}
                    {!! Form::select('group_id', [''=>'Wähle Gruppe'] + $groups, null, ['class' => 'form-control']) !!}
                </div>
            @endif
            <div class="form-group">
                {!! Form::submit('Update Adresse', ['class' => 'btn btn-primary'])!!}
            </div>
         {!! Form::close()!!}

         {!! Form::model($address, ['method' => 'DELETE', 'action'=>['AdminAddressesController@destroy',$address->id]]) !!}
         <div class="form-group">
             {!! Form::submit('Adresse löschen', ['class' => 'btn btn-danger'])!!}
         </div>
      {!! Form::close()!!}
    </div>    
 

@endsection

@section('scripts')
        <script type="text/javascript">

        //autocomplete script
        $(document).on('focus','.autocomplete_txt',function(){
        type = $(this).attr('name');

        if(type =='city_name' )autoType='name'; 
        if(type =='city_plz' )autoType='plz'; 

        $(this).autocomplete({
            minLength: 0,
            source: function( request, response ) {
                    $.ajax({
                        url: "{{ route('searchajaxcity') }}",
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
                $('#city_name').val(data.name);
                $('#city_plz').val(data.plz);
            }
        });
        
        
        });
        </script>    
    @endsection