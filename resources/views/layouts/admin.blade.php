<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@include('includes/header')

<body class="text-dark__black">
    <div class="antialiased page">
        @include('includes/admin_topnav')

        <main class="p-4 h-auto pt-20">
            @yield('content')
        </main>

        <x-footer />
    </div>

    <!-- jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script type="module">
        window.Echo.private(`notification-create.${window.actionID}`)
            .listen('NotificationCreate', (e) => {
                UpdateNotifications(e);
            });
    </script>
    @stack('scripts')
</body>

</html>