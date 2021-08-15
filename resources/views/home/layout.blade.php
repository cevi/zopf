@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">

            <!-- Blog Post Content Column -->
            <div class="col-lg-12">

             @yield('route_content')   

            </div>

        </div>
    </div>
@endsection