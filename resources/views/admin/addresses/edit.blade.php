@extends('layouts.admin')

@section('content')
<div class="breadcrumb-holder">
    <div class="container-fluid">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/admin/addresses">Adressen</a></li>
            <li class="breadcrumb-item active">Bearbeiten</li>
        </ul>
    </div>
</div>
<section>
    <div class="container-fluid">
        <!-- Page Header-->
        <header>
            <h1 class="h3 display">Adressen bearbeiten</h1>
        </header>
        <div class="row">
            <form method="POST" action="{{ action('AdminAddressesController@update', $address->id) }}" accept-charset="UTF-8">
                @csrf
                @method('PATCH')
            <div class="form-row">
                <div class="form-group col-md-6">
                    <x-form.label for="firstname">Vorname:</x-form.label>
                    <x-form.input name="firstname" :model="$address" id="firstname" class="form-control" />
                </div>
                <div class="form-group col-md-6">
                    <x-form.label for="name">Name:</x-form.label>
                    <x-form.input name="name" :model="$address" id="name" class="form-control" />
                </div>
            </div>

            <div class="form-group">
                <x-form.label for="street">Strasse:</x-form.label>
                <x-form.input name="street" :model="$address" id="street" class="form-control" />
            </div>

            <div class="form-row">
                <div class="form-group col-md-3">
                    <x-form.label for="city_plz">PLZ:</x-form.label>
                    <x-form.input name="city_plz" :value="$city_plz" :model="$address" id="city_plz" class="form-control autocomplete_txt" />
                </div>
                <div class="form-group col-md-9">
                    <x-form.label for="city_name">Ort:</x-form.label>
                    <x-form.input name="city_name" :value="$city_name" :model="$address" id="city_name" class="form-control autocomplete_txt" />
                </div>
                <x-form.input name="city_id" type="hidden" :value="$city_id" :model="$address" class="form-control autocomplete_txt" />
            </div>

            @if (Auth::user()->isAdmin())
            <div class="form-group">
                <x-form.label for="group_id">Gruppe:</x-form.label>
                <x-form.select name="group_id" :options="['' => 'Wähle Gruppe'] + $groups" :model="$address" id="group_id" class="form-control" />
            </div>
            @endif

            <div class="form-group">
                <x-form.submit value="Update Adresse" class="btn btn-primary" />
            </div>
            </form>

            <form method="POST" action="{{ action('AdminAddressesController@destroy', $address->id) }}" accept-charset="UTF-8">
                @csrf
                @method('DELETE')
            <div class="form-group">
                <x-form.submit value="Adresse löschen" class="btn btn-danger" />
            </div>
            </form>
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
                        console.log(data);
                        $("[name='city_name']").val(data.name);
                        $("[name='city_plz']").val(data.plz);
                        $("[name='city_id']").val(data.id);
                    }
                });


            });
        }, false);
</script>
@endpush