@extends('layouts.admin')

@section('content')
<!-- Page Header-->
<x-page-title :title="$title" :help="$help" :header=false />
@if(Auth::user()->isActionleader() && Auth::user()->action)
<!-- Counts Section -->
<section class="dashboard-counts section-padding">
    <div class="container-fluid">
        <div class="row">
            @foreach ($icon_array as $icon)
            <div class="col-xl-2 col-md-4 col-6">
                <div id="{{$icon->id}}" class="wrapper count-title d-flex">
                    <div class="icon"><i class="fa-solid {{$icon->icon}}"></i></div>
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
<section class="section-padding">
    <div class="container-fluid">
        <div class="row d-flex">
            <div class="col-lg-6 col-md-12">
                <div href="#"
                    class="open-routes block p-6 bg-white border border-gray-200 rounded-lg shadow-md dark:bg-gray-800 dark:border-gray-700 updates user-activity">
                    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                        Übersicht</h5>
                    <!-- Pie Chart-->
                    <div style="height: 400px;">
                        {!! $zopfChart->container() !!}
                    </div>
                </div>
            </div>
            <!-- Line Chart -->
            <div class="col-lg-6 col-md-12">
                <div href="#"
                    class=" open-routes block p-6 bg-white border border-gray-200 rounded-lg shadow-md dark:bg-gray-800 dark:border-gray-700 updates user-activity">
                    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                        Zeitlicher Verlauf</h5>
                    <div class="line-chart">
                        {{-- <canvas id="lineChart"></canvas>--}}
                        {!! $timeChart->container() !!}
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
            <div class="col-lg-6 col-md-12">
                <div id="open_routes" href="#"
                    class=" open-routes block p-6 bg-white border border-gray-200 rounded-lg shadow-md dark:bg-gray-800 dark:border-gray-700 updates user-activity">
                    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Routen</h5>
                    @if($open_routes)
                    @foreach ($open_routes as $key => $route)
                    <div id="route-{{$route['id']}}" data-count-open="{{$route->zopf_open_count()}}"
                        data-count-all="{{$route->zopf_count()}}">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td style="width:5%">
                                        <h3 class="h4 display">{{$key + 1 }}</h3>
                                    </td>
                                    <td style="width:25%"><a href="{{route('routes.overview', $route->id)}}"
                                            class="font-medium text-blue-600 dark:text-blue-500 hover:underline">
                                            <h3 class="h4 display">{{$route->name}}</h3>
                                        </a></td>
                                    <td style="width:25%">
                                        <h3 class="h4 display">{{$route->user['username']}}</h3>
                                    </td>
                                    <td style="width:20%">
                                        <h3 class="h4 display">{{$route->route_type ? $route->route_type['name'] : ''}}
                                        </h3>
                                    </td>
                                    <td style="width:25%">
                                        <h3 class="h4 display">{{$route->route_status['name']}}</h3>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="progress">
                            <div id="route-progessbar-{{$route['id']}}" role="progressbar"
                                style="width:  {{$route->route_done_percent()}}%"
                                aria-valuenow=" {{$route->route_done_percent()}}" aria-valuemin="0" aria-valuemax="100"
                                class="progress-bar progress-bar bg-primary"></div>
                        </div>
                        <div class="page-statistics d-flex justify-content-between">
                            <div class="page-statistics-left">
                                <span>Zöpfe Total</span><strong>{{$route->zopf_count()}}</strong>
                            </div>
                            <div id="route-open-{{$route['id']}}" class="page-statistics-right">
                                <span>Noch Offen</span><strong>{{$route->zopf_open_count()}}</strong>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>
            <div class="col-lg-6 col-md-12">
                <!-- Recent Activities Widget      -->
                <div href="#"
                    class="block p-6 bg-white border border-gray-200 rounded-lg shadow-md dark:bg-gray-800 dark:border-gray-700">
                    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                        Logbuch</h5>
                    <div id="logbookList" class="activities">
                        <ol class="relative border-l border-gray-200 dark:border-gray-700">
                            <!-- Item-->
                            @if($notifications)
                            @foreach ($notifications as $notification)
                            <x-logbook-item :user="$notification['user']" :content="$notification['content']"
                                :time="$notification['when']" :id="$notification['id']" />
                            @endforeach
                            @endif
                        </ol>
                    </div>
                    @if($users)
                    <div role="tabpanel">
                        @include('includes.form_error')
                        {!! Form::open(['method' => 'POST', 'action'=>'AdminController@logcreate']) !!}
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                {!! Form::label('when', 'Wann:', ['class' =>'block mb-2 text-sm font-medium
                                text-gray-900 dark:text-white']) !!}
                                {!! Form::time('when', now(), ['class' => 'bg-gray-50 border border-gray-300
                                text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full
                                p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white
                                dark:focus:ring-blue-500 dark:focus:border-blue-500']) !!}
                            </div>
                            <div class="form-group col-md-6">
                                {!! Form::label('user_id', 'Verantwortlicher:', ['class' =>'block mb-2 text-sm
                                font-medium text-gray-900 dark:text-white']) !!}
                                {!! Form::select('user_id', [''=>'Wähle Leiter'] + $users, null, ['class' =>
                                'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500
                                focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600
                                dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500
                                dark:focus:border-blue-500']) !!}
                            </div>
                            <div class="form-group col-md-3">
                                {!! Form::label('cut', 'Anzahl Aufgeschnitten:', ['class' =>'block mb-2 text-sm
                                font-medium text-gray-900 dark:text-white']) !!}
                                {!! Form::text('cut', null, ['class' => 'bg-gray-50 border border-gray-300 text-gray-900
                                text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5
                                dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white
                                dark:focus:ring-blue-500 dark:focus:border-blue-500']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('comments', 'Kommentar:', ['class' =>'block mb-2 text-sm font-medium
                            text-gray-900 dark:text-white']) !!}
                            {!! Form::text('comments', null, ['class' => 'bg-gray-50 border border-gray-300
                            text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full
                            p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white
                            dark:focus:ring-blue-500 dark:focus:border-blue-500']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::submit('Eintrag Erfassen', ['class' => 'text-white bg-blue-700 hover:bg-blue-800
                            focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2
                            dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800'])!!}
                        </div>
                        {!! Form::close()!!}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@else
<section class="dashboard-counts section-padding">
    <div class="container-fluid">
        <h3>Du hast noch keine zugeordnete Aktion, erstelle <a href="{{ route('actions.create') }}">hier</a>
            eine Aktion.</h3>
    </div>
</section>
@endif

@endsection

@push('scripts')
    {{isset($zopfChart) ? $zopfChart->script() : ''}}
    {{isset($timeChart) ? $timeChart->script() : ''}}

    <script type="module">
        function UpdateGraph() {
            {{ $zopfChart->id }}_refresh({{ $zopfChart->id }}_api_url);
            {{ $timeChart->id }}_refresh({{ $timeChart->id }}_api_url);
        }

        function UpdateList(input) {
            $.ajax({
                url: "{{ route('logbooks.index') }}",
                dataType: 'json',
                success: function (data) {
                    var logbook = $("#logbookList");
                    // data = JSON.parse(data);
                    var Notification = '<ol class="relative border-l border-gray-200 dark:border-gray-700">';
                    var Symbol = '<li class="mb-10 ml-6">' +
                        '<span class="flex absolute -left-3 justify-center items-center w-6 h-6 bg-blue-200 rounded-full ring-8 ring-white dark:ring-gray-900 dark:bg-blue-900">' +
                        '<svg aria-hidden="true" class="w-3 h-3 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">' +
                        '<path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd">' +
                        '</path>' +
                        '</svg>' +
                        '</span>';

                    Object.values(data).forEach((element) => {
                        var route = "{{route('logbooks.edit', '')}}" + element.id + "edit";
                        Notification += Symbol +
                            '<div class="justify-between items-center p-4 bg-white rounded-lg border border-gray-200 shadow-sm sm:flex dark:bg-gray-700 dark:border-gray-600">' +
                            '<time class="mb-1 text-xs font-normal text-gray-400 sm:order-last sm:mb-0">' + element.when + '</time>' +
                            '<div class="text-sm font-normal text-gray-500 dark:text-gray-300">' +
                            '<strong><a href="' + route + '">' + element.user + ': </a></strong>' +
                            element.content +
                            '</div>' +
                            '</div>' +
                            '</li>';
                    });
                    Notification += '</ol>';
                    logbook.html(Notification);
                },
                error: function (data) {
                    console.log(data);
                }
            });
        }


        function UpdateIconArray(action) {
            $.ajax({
                url: "{{ route('icon_array.get') }}",
                dataType: 'json',
                data: {
                    action: action
                },
                success: function (data) {
                    data.forEach(WriteIconArray);

                    function WriteIconArray(item) {
                        var iconElement = $('#' + item['id']);
                        var iconNumber = iconElement.find('.count-number');
                        iconNumber.html(item['number']);
                    }
                },
                error: function (data) {
                    console.log(data);
                }
            });
        }
    </script>
@endpush