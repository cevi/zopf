<!doctype html>
<html lang="{{ app()->getLocale()}}">
@include('includes/header')
<body>
<div id="app" class="page mainpage text-dark__black">
    @include('includes/topnav')


    <main class="py-4">
        @yield('content')
    </main>
    <x-footer/>
</div>
<!-- jQuery -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.6.5/flowbite.min.js"></script>
@yield('scripts')
</body>
</html>
