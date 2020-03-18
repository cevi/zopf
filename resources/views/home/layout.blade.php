@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">

            <!-- Blog Post Content Column -->
            <div class="col-lg-8">

             @yield('route_content')   

            </div>

            <!-- Blog Sidebar Widgets Column -->
            <div class="col-md-4">
                <!-- Blog Categories Well -->
                <div class="well">
                    <h4>Deine Routen</h4>
                    @if ($routes)
                        
                        <ul class="list-unstyled">
                            @foreach ($routes as $route)
        
                                <li><a href="{{route('home.routes',$route->id)}}">{{$route->name}}</a></li>
                            @endforeach
                        </ul>
                    @endif
                    <!-- /.row -->
                </div>
            </div>

        </div>
    </div>
@endsection