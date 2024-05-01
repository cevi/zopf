@extends('layouts.admin')
@section('content')
<div class="breadcrumb-holder">
    <div class="container-fluid">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/admin/addresses">Adressen</a></li>
            <li class="breadcrumb-item active">Erfassen</li>
        </ul>
        </ul>
    </div>
</div>
<section>
    <div class="container-fluid">
        <!-- Page Header-->
        <header>
            <h1 class="h3 display">Adressen erfassen</h1>
        </header>
        <div class="row">

            <div class="col-sm-6">
                @include('includes.form_error')
                {!! Form::open(['method' => 'POST', 'action'=>'AdminAddressesController@store']) !!}
                <div class="form-row">
                    <div class="form-group col-md-6">
                        {!! Form::label('firstname', 'Vorname:') !!}
                        {!! Form::text('firstname', null, ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group col-md-6">
                        {!! Form::label('name', 'Name:') !!}
                        {!! Form::text('name', null, ['class' => 'form-control']) !!}
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::label('street', 'Strasse:') !!}
                    {!! Form::text('street', null, ['class' => 'form-control']) !!}
                </div>

                <div class="form-row">
                    <div class="form-group col-md-3">
                        {!! Form::label('city_plz', 'PLZ:') !!}
                        {!! Form::text('city_plz', null, ['class' => 'form-control autocomplete_txt']) !!}
                    </div>
                    <div class="form-group col-md-9">
                        {!! Form::label('city_name', 'Ort:') !!}
                        {!! Form::text('city_name', null, ['class' => 'form-control autocomplete_txt']) !!}
                    </div>
                    {!! Form::hidden('city_id', null, ['class' => 'form-control autocomplete_txt']) !!}
                </div>

                @if (Auth::user()->isAdmin())
                <div class="form-group">
                    {!! Form::label('group_id', 'Gruppe:') !!}
                    {!! Form::select('group_id', [''=>'Wähle Gruppe'] + $groups, null, ['class' => 'form-control']) !!}
                </div>
                @endif

                <div class="form-group">
                    {!! Form::submit('Adresse Erfassen', ['class' => 'btn btn-primary'])!!}
                </div>
                {!! Form::close()!!}
            </div>
        </div>
    </div>
</section>
@endsection


@push('scripts')
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function () {
            //autocomplete script
            $(document).on('focus', '.autocomplete_txt', function () {
                type = $(this).attr('name');

                if (type == 'city_name') autoType = 'name';
                if (type == 'city_plz') autoType = 'plz';
                if (type == 'city_id') autoType = 'id';

                $(this).autocomplete({
                    minLength: 2,
                    highlight: true,
                    source: function (request, response) {
                        $.ajax({
                            url: "{{ route('searchajaxcity') }}",
                            dataType: "json",
                            data: {
                                term: request.term,
                                type: type,
                            },
                            success: function (data) {
                                var array = $.map(data, function (item) {
                                    return {
                                        label: item['plz'] + ' ' + item['name'],
                                        value: item[autoType],
                                        data: item
                                    }
                                });
                                response(array)
                            }
                        });
                    },
                    select: function (event, ui) {
                        var data = ui.item.data;
                        $("[name='city_name']").val(data.name);
                        $("[name='city_plz']").val(data.plz);
                        $("[name='city_id']").val(data.id);
                    }
                });


            });
        }, false);
</script>
@endpush