<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@include('includes/header')
<body>
<div class="page">
    @include('includes/admin_topnav')
    @include('includes/admin_sidenav')

    @yield('content')
    <x-footer/>
</div>

<!-- jQuery -->
<script src="//js.pusher.com/3.1/pusher.min.js"></script>
<script src="{{asset('js/libs.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="https://unpkg.com/flowbite@1.5.4/dist/flowbite.js"></script>
@yield('scripts')
</body>

</html>
