@extends('layouts.admin')

@section('content')

    <div class="breadcrumb-holder">
        <div class="container-fluid">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="/admin/progress">Backstuben Verlauf</a></li>
                <li class="breadcrumb-item active">Bearbeiten</li>
            </ul>
        </div>
    </div>
    <section>
        <div class="container-fluid">
            <header>
                <h1 class="h3 display">{{$title}}</h1>
            </header>
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
