@extends('layouts.admin')

@section('content')

    <x-page-title :title="$title" :help="$help"/>
    <section>
        <div class="container-fluid">
            <div class="col-sm-6">
                {!! Form::model($progress, ['method' => 'Patch', 'action'=>['AdminBakeryProgressController@update',$progress]]) !!}
                <div class="form-group">
                    {!! Form::label('when', 'Wann:') !!}
                    {!! Form::time('when', null, ['class' => 'form-control']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('raw_material', 'Roh-Materialien:') !!}
                    {!! Form::number('raw_material', null, ['class' => 'form-control']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('dough', 'Teig:') !!}
                    {!! Form::number('dough', null, ['class' => 'form-control']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('braided', 'Gezöpfelt:') !!}
                    {!! Form::number('braided', null, ['class' => 'form-control']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('baked', 'Gebacken:') !!}
                    {!! Form::number('baked', null, ['class' => 'form-control']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('delivered', 'Ausgeliefert:') !!}
                    {!! Form::number('delivered', null, ['class' => 'form-control']) !!}
                </div>
                <div class="form-group">
                    {!! Form::submit('Eintrag bearbeiten', ['class' => 'btn btn-primary'])!!}
                </div>
                {!! Form::close()!!}

                {!! Form::model($progress, ['method' => 'DELETE', 'action'=>['AdminBakeryProgressController@destroy',$progress]]) !!}
                <div class="form-group">
                    {!! Form::submit('Eintrag löschen', ['class' => 'btn btn-danger'])!!}
                </div>
                {!! Form::close()!!}
            </div>
        </div>

    </section>
@endsection
