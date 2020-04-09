@extends('layouts.admin')

@section('content')
    
    <!-- Counts Section -->
	<section class="dashboard-counts section-padding">
    <div class="container-fluid">
      <div class="row">
        <!-- Count item widget-->
        <div class="col-xl-2 col-md-4 col-6">
          <div class="wrapper count-title d-flex">
            <div class="icon"><i class="icon-padnote"></i></div>
            <div class="name"><strong class="text-uppercase">Zöpfe</strong>
              <div class="count-number">{{$total ?? ''}}</div>
            </div>
          </div>
        </div>
        <!-- Count item widget-->
        <div class="col-xl-2 col-md-4 col-6">
          <div class="wrapper count-title d-flex">
            <div class="icon"><i class="icon-padnote"></i></div>
            <div class="name"><strong class="text-uppercase">Bestellungen</strong>
              <div class="count-number">{{$orders_count}}</div>
            </div>
          </div>
        </div>
        <!-- Count item widget-->
        <div class="col-xl-2 col-md-4 col-6">
          <div class="wrapper count-title d-flex">
            <div class="icon"><i class="icon-check"></i></div>
            <div class="name"><strong class="text-uppercase">Routen</strong>
              <div class="count-number">{{$routes_count}}</div>
            </div>
          </div>
        </div>
        <!-- Count item widget-->
        <div class="col-xl-2 col-md-4 col-6">
          <div class="wrapper count-title d-flex">
            <div class="icon"><i class="icon-bill"></i></div>
            <div class="name"><strong class="text-uppercase">Offen</strong>
              <div class="count-number">{{$orders_open_delivery}}</div>
            </div>
          </div>
        </div>
        <!-- Count item widget-->
        <div class="col-xl-2 col-md-4 col-6">
          <div class="wrapper count-title d-flex">
            <div class="icon"><i class="icon-user"></i></div>
            <div class="name"><strong class="text-uppercase">Abholen</strong>
              <div class="count-number">{{$orders_open_pickup}}</div>
            </div>
          </div>
        </div>
        <!-- Count item widget-->
        <div class="col-xl-2 col-md-4 col-6">
          <div class="wrapper count-title d-flex">
            <div class="icon"><i class="icon-list-1"></i></div>
            <div class="name"><strong class="text-uppercase">Aufgeschnitten</strong>
              <div class="count-number">{{$cut}}</div>
            </div>
          </div>
        </div>
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
            <h2 class="display h4">Project Beta progress</h2>
            <p> Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
            <div class="pie-chart">
              <canvas id="pieChartZopf" width="300" height="300"> </canvas>
            </div>
          </div>
        </div>
        <!-- Line Chart -->
        <div class="col-lg-5 col-md-12 flex-lg-last flex-md-first align-self-baseline">
          <div class="card sales-report">
            <h2 class="display h4">Sales marketing report</h2>
            <p> Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dolor amet officiis</p>
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
          <!-- User Actibity-->
          <div class="card user-activity">
            <h2 class="display h4">Offene Routen</h2>
            <h3 class="h4 display">Routen Name 1</h3>
            <div class="progress">
              <div role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100" class="progress-bar progress-bar bg-primary"></div>
            </div>
            <div class="page-statistics d-flex justify-content-between">
              <div class="page-statistics-left"><span>Zöpfe Total</span><strong>230</strong></div>
              <div class="page-statistics-right"><span>Noch Offen</span><strong>73.4%</strong></div>
            </div>
            <h3 class="h4 display">Routen Name 2</h3>
            <div class="progress">
              <div role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100" class="progress-bar progress-bar bg-primary"></div>
            </div>
            <div class="page-statistics d-flex justify-content-between">
              <div class="page-statistics-left"><span>Zöpfe Total</span><strong>230</strong></div>
              <div class="page-statistics-right"><span>Noch Offen</span><strong>73.4%</strong></div>
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
                @foreach ($logbooks as $logbook)
                  <li>
                    <div class="row">
                      <div class="col-4 date-holder text-right">
                        <div class="icon"><i class="icon-clock"></i></div>
                        <div class="date"> <span>{{$logbook->created_at->toTimeString()}}</span><span class="text-info">{{$logbook->created_at->diffForHumans()}}</span></div>
                      </div>
                      <div class="col-8 content"><strong>{{$logbook->user['username']}}</strong>
                      <p>{{$logbook->comments}}</p>
                      </div>
                    </div>
                  </li>    
                @endforeach
              </ul>
            </div>  
            <div role="tabpanel">
              <h2 class="display h4">Sales marketing report</h2>
              Form für Logbuch
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

@endsection

@section('scripts')
<script>
$(document).ready(function () {

'use strict';

var brandPrimary = '#74C5AD';

var PIECHARTZOPF    = $('#pieChartZopf')

  var pieChartZopf = new Chart(PIECHARTZOPF, {
        type: 'doughnut',
        data: {
            labels: [
                "Offen",
                "Unterwegs",
                "Abzuholen",
                "Aufgeschnitten",
                "Abgeschlossen"
            ],
            datasets: [
                {
                    data: [{{$cut}}, {{$cut}}, {{$cut}}, {{$cut}}, {{$cut}}],
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

