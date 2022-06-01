@extends('layouts.admin')

@section('content')
    <div class="breadcrumb-holder">
        <div class="container-fluid">
          <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="admin/">Dashboard</a></li>
            <li class="breadcrumb-item active">{{$title}}</li>
          </ul>
        </div>
    </div>
    <section>
        <div class="container-fluid">
            <!-- Page Header-->
            <header>
                <h1 class="h3 display">{{$title}}</h1>
            </header>
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
                        {!! Form::submit('Eintrag erstellen', ['class' => 'btn btn-primary'])!!}
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
                        <th scope="col">Aktionen</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($progress as $act_progress)
                        <tr>
                            <td>{{$act_progress['when']}}</td>
                            <td>{{$act_progress['raw_material']}}</td>
                            <td>{{$act_progress['dough']}}</td>
                            <td>{{$act_progress['braided']}}</td>
                            <td>{{$act_progress['baked']}}</td>
                            <td>{{$act_progress['delivered']}}</td>
                            <td>{{$act_progress['total']}}</td>
                            <td>
                                <a href="{{route('progress.edit', $act_progress)}}" type="button" class="btn btn-success btn-sm">Bearbeiten</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <br>
            @if(count($progress)>1)
                <h4>Zeitlicher Verlauf</h4>
                <div class="area-chart">
                    <canvas id="areaChart"></canvas>
                </div>
            @endif
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            'use strict';

            var brandPrimary = '#74C5AD';

            var AREACHART   = $('#areaChart');

            var AreaChart = new Chart(AREACHART, {
                type: 'line',
                data: {
                    labels: @json($graphs[0]['time']),
                    datasets: [
                        @for($i=1; $i<=5; $i++)
                            {
                                label: @json($graphs[$i]['label']),
                                data: @json($graphs[$i]['data']),
                                borderColor: @json($graphs[$i]['color']),
                                backgroundColor: @json($graphs[$i]['color']),
                                fill: true
                            },
                        @endfor
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        tooltip: {
                            mode: 'index'
                        },
                    },
                    interaction: {
                        mode: 'nearest',
                        axis: 'x',
                        intersect: false
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Uhrzeit'
                            }
                        },
                        y: {
                            stacked: true,
                            title: {
                                display: true,
                                text: 'Anzahl Zöpfe'
                            }
                        }
                    }
                }
            });
        });
    </script>

@endsection
