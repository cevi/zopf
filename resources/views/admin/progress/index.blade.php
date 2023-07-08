@extends('layouts.admin')

@section('content')
    <x-page-title :title="$title" :help="$help"/>
    <section>
        <div class="container-fluid">
            <!-- Page Header-->
            <br>
            @if(count($progress)>1)
                <div class="area-chart">
                    {!! $progressChart->container() !!}
                </div>
                <br>
            @endif
            {!! Form::open(['method' => 'POST', 'action'=>'AdminBakeryProgressController@store']) !!}
            <div class="row">
                <div class="form-group col-xl-1 col-6">
                    {!! Form::label('when', 'Wann:') !!}
                    {!! Form::time('when', now(), ['class' => 'form-control']) !!}
                </div>
                <div class="form-group col-xl-2 col-6">
                    {!! Form::label('raw_material', 'Roh-Materialien:') !!}
                    {!! Form::number('raw_material', null, ['class' => 'form-control']) !!}
                </div>
                <div class="form-group col-xl-2 col-6">
                    {!! Form::label('dough', 'Teig:') !!}
                    {!! Form::number('dough', null, ['class' => 'form-control']) !!}
                </div>
                <div class="form-group col-xl-2 col-6">
                    {!! Form::label('braided', 'Gezöpfelt:') !!}
                    {!! Form::number('braided', null, ['class' => 'form-control']) !!}
                </div>
                <div class="form-group col-xl-2 col-6">
                    {!! Form::label('baked', 'Gebacken:') !!}
                    {!! Form::number('baked', null, ['class' => 'form-control']) !!}
                </div>
                <div class="form-group col-xl-2 col-6">
                    {!! Form::label('delivered', 'Ausgeliefert:') !!}
                    {!! Form::number('delivered', null, ['class' => 'form-control']) !!}
                </div>
                <div class="form-group col-xl-1 col-2">
                    <br>
                    {!! Form::submit('Eintrag Erfassen', ['class' => 'btn btn-primary'])!!}
                </div>
            </div>
            {!! Form::close()!!}
            <table class="table table-striped">
                <thead>
                <tr>
                    <th scope="col">Wann</th>
                    <th scope="col">Roh-Materialien</th>
                    <th scope="col">Teig</th>
                    <th scope="col">Gezöpfelt</th>
                    <th scope="col">Gebacken</th>
                    <th scope="col">Ausgeliefert</th>
                    <th scope="col">Total</th>
                </tr>
                </thead>
                <tbody>
                @foreach($progress as $act_progress)
                    <tr>
                        <td><a href="{{route('progress.edit', $act_progress)}}">{{$act_progress['when']}}</a></td>
                        <td>{{$act_progress['raw_material']}}</td>
                        <td>{{$act_progress['dough']}}</td>
                        <td>{{$act_progress['braided']}}</td>
                        <td>{{$act_progress['baked']}}</td>
                        <td>{{$act_progress['delivered']}}</td>
                        <td>{{$act_progress['total']}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection

@section('scripts')
    {{isset($progressChart) ? $progressChart->script() : ''}}

@endsection
