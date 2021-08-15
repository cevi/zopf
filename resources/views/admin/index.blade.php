@extends('layouts.admin')

@section('content')
    
    <!-- Counts Section -->
	<section class="dashboard-counts section-padding">
    <div class="container-fluid">
      <div class="row">
        @foreach ($icon_array as $icon)
          <div class="col-xl-2 col-md-4 col-6">
            <div class="wrapper count-title d-flex">
              <div class="icon"><i class={{$icon->icon}}></i></div>
              <div class="name"><strong class="text-uppercase">{{$icon->name}}</strong>
                <div class="count-number">{{$icon->number}}</div>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </section>
  <!-- Header Section-->
  <section class="dashboard-header section-padding">
    <div class="container-fluid">
      <div class="row d-flex align-items-md-stretch">       
        <!-- Pie Chart-->
        <div class="col-lg-5 col-md-12">
          <div class="card project-progress">
            <h2 class="display h4">Übersicht</h2>
            <div class="pie-chart">
              <canvas id="pieChartZopf" width="300" height="300"> </canvas>
            </div>
          </div>
        </div>
        <!-- Line Chart -->
        <div class="col-lg-5 col-md-12 flex-lg-last flex-md-first align-self-baseline">
          <div class="card sales-report">
            <h2 class="display h4">Zeitlicher Verlauf</h2>
            <div class="line-chart">
              <canvas id="lineChart"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- Statistics Section-->
  <section class="statistics">
    <div class="container-fluid">
      <div class="row d-flex">
        <div class="col-lg-6 col-md-6">
          <div id="open-routes-wrapper" class="card updates user-activity">
            <div id="activites-header" class="card-header d-flex justify-content-between align-items-center">
              <h2 class="h5 display"><a data-toggle="collapse" data-parent="#open-routes-wrapper" href="#open-routes-box" aria-expanded="true" aria-controls="open-routes-box">Routen</a></h2><a data-toggle="collapse" data-parent="#open-routes-wrapper" href="#open-routes-box" aria-expanded="true" aria-controls="open-routes-box"><i class="fa fa-angle-down"></i></a>
            </div>
            <div id="open-routes-box" role="tabpanel" class="open-routes collapse show">
              @if($open_routes)
                @foreach ($open_routes as $key => $route)
                <table class="table">
                    <tbody>
                        <tr>
                            <td style="width:5%"><h3 class="h4 display">{{$key + 1 }}</h3></td>
                            <td style="width:25%"><h3 class="h4 display">{{$route->name}}</h3></td>
                            <td style="width:25%"><h3 class="h4 display">{{$route->user['username']}}</h3></td>
                            <td style="width:20%"><h3 class="h4 display">{{$route->route_type['name']}}</h3></td>
                            <td style="width:25%"><h3 class="h4 display">{{$route->route_status['name']}}</h3></td>
                        </tr>
                    </tbody>
                </table>
                  <div class="progress">
                    <div role="progressbar" style="width:  {{$route->route_done_percent()}}%" aria-valuenow=" {{$route->route_done_percent()}}" aria-valuemin="0" aria-valuemax="100" class="progress-bar progress-bar bg-primary"></div>
                  </div>
                  <div class="page-statistics d-flex justify-content-between">
                    <div class="page-statistics-left"><span>Zöpfe Total</span><strong>{{$route->zopf_count()}}</strong></div>
                    <div class="page-statistics-right"><span>Noch Offen</span><strong>{{$route->zopf_open_count()}}</strong></div>
                  </div>
                @endforeach
              @endif
            </div>
          </div>
        </div>
        <div class="col-lg-6 col-md-6">
          <!-- Recent Activities Widget      -->
          <div id="recent-activities-wrapper" class="card updates activities">
            <div id="activites-header" class="card-header d-flex justify-content-between align-items-center">
              <h2 class="h5 display"><a data-toggle="collapse" data-parent="#recent-activities-wrapper" href="#activities-box" aria-expanded="true" aria-controls="activities-box">Logbuch</a></h2><a data-toggle="collapse" data-parent="#recent-activities-wrapper" href="#activities-box" aria-expanded="true" aria-controls="activities-box"><i class="fa fa-angle-down"></i></a>
            </div>
            <div id="activities-box" role="tabpanel" class="collapse show">
              <ul class="activities list-unstyled">
                <!-- Item-->
                @if($logbooks)
                  @foreach ($logbooks as $logbook)
                    <li>
                      <div class="row">
                        <div class="col-4 date-holder text-right">
                          <div class="icon"><i class="icon-clock"></i></div>
                          <div class="date"> <span>{{$logbook->wann}}</span><span class="text-info"></span></div>
                        </div>
                        <div class="col-8 content"><strong>{{$logbook->user['username']}}</strong>
                        <p>{{$logbook->comments}}</p>
                        </div>
                      </div>
                    </li>    
                  @endforeach
                @endif
              </ul>
            </div>  
            @if($users)
              <div role="tabpanel">
                <h2 class="display h4">Form</h2>
                @include('includes.form_error')
                {!! Form::open(['method' => 'POST', 'action'=>'AdminController@logcreate']) !!}
                  <div class="form-row">
                    <div class="form-group col-md-3">  
                        {!! Form::label('wann', 'Wann:') !!}
                        {!! Form::time('wann', now(), ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group col-md-6">  
                        {!! Form::label('user_id', 'Verantwortlicher:') !!}
                        {!! Form::select('user_id', [''=>'Wähle Leiter'] + $users, null, ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group col-md-3">  
                        {!! Form::label('cut', 'Anzahl Aufgeschnitten:') !!}
                        {!! Form::text('cut', null, ['class' => 'form-control']) !!}
                    </div>
                  </div>
                  <div class="form-group">
                    {!! Form::label('comments', 'Kommentar:') !!}
                    {!! Form::text('comments', null, ['class' => 'form-control']) !!}
                  </div>
                  <div class="form-group">
                      {!! Form::submit('Eintrag Erstellen', ['class' => 'btn btn-primary'])!!}
                  </div>
                {!! Form::close()!!}
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </section>

@endsection

@section('scripts')
<script>
// setInterval(function() { 
//   console.log('Hallo');
//   $.get("/admin", function(data, status){ 
// 	  $("body").html(data); 
//   }); 
// }, 10000); // will refresh every 5 seconds = 5000 ms 
$(document).ready(function () {

'use strict';

var brandPrimary = '#74C5AD';

var LINECHART   = $('#lineChart'),
    PIECHARTZOPF    = $('#pieChartZopf');

var lineChart = new Chart(LINECHART, {
        type: 'line',
        data: {
            labels: @json($graphs_time),
            datasets: [
                {
                    label: "Anzahl Zöpfe",
                    fill: true,
                    lineTension: 0.3,
                    backgroundColor: "rgba(51, 179, 90, 0.38)",
                    borderColor: brandPrimary,
                    borderCapStyle: 'butt',
                    borderDash: [],
                    borderDashOffset: 0.0,
                    borderJoinStyle: 'miter',
                    borderWidth: 1,
                    pointBorderColor: brandPrimary,
                    pointBackgroundColor: "#fff",
                    pointBorderWidth: 1,
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: brandPrimary,
                    pointHoverBorderColor: "rgba(220,220,220,1)",
                    pointHoverBorderWidth: 2,
                    pointRadius: 1,
                    pointHitRadius: 10,
                    data: @json($graphs_sum),
                    spanGaps: false
                }
            ]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });

  var pieChartZopf = new Chart(PIECHARTZOPF, {
        type: 'doughnut',
        data: {
            labels: [
                "Offen",
                "Unterwegs",
                "Abholen",
                "Aufgeschnitten",
                "Abgeschlossen"
            ],
            datasets: [
                {
                    data: [{{$orders_open}}, {{$orders_delivery}}, {{$orders_open_pickup}}, {{$cut}}, {{$orders_finished}}],
                    borderWidth: [1, 1, 1, 1],
                    backgroundColor: [
                        "#E6DADA",
                        brandPrimary,
                        "#93E6CB",
                        "#E6BF91",
                        "#E69C5B"
                    ],
                    hoverBackgroundColor: [
                        "#CFC4C4",
                        "#68B19C",
                        "#84CFB7",
                        "#CFAC83",
                        "#CF8C52"
                    ]
                }]
            }
    });

    var pieChart = {
        responsive: true
    };
  });
</script>
    
@endsection

