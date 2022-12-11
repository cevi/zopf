<head>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Zopfaktion">
    <meta name="author" content="Jérôme Sigg v/o Amigo">
    <meta name="robots" content="all,follow">

    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#00aba9">
    <meta name="theme-color" content="#ffffff">

    <title>{{isset($title) ? $title . ' - ' : ''}}{{config('app.name')}}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">
    <script src="https://kit.fontawesome.com/da9e6dcf22.js" crossorigin="anonymous"></script>

    <!-- Styles -->
    <link rel="stylesheet" href="https://unpkg.com/flowbite@1.5.4/dist/flowbite.min.css"/>
    <link href="{{asset('css/app.css')}}" rel="stylesheet">
    <link href="{{asset('css/libs.css')}}" rel="stylesheet">
    <script>
        // It's best to inline this in `head` to avoid FOUC (flash of unstyled content) when changing pages or themes
        if (localStorage.getItem('color-theme') === 'dark' ||
            (!('color-theme' in localStorage) &&
                window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            // document.documentElement.classList.add('dark');
        } else {
            // document.documentElement.classList.remove('dark');
        }
    </script>
    <script type="text/javascript">
        @if(isset($smartsupp_token))
        var _smartsupp = _smartsupp || {};
        _smartsupp.key = @json($smartsupp_token);
        window.smartsupp || (function (d) {
            var s, c, o = smartsupp = function () {
                o._.push(arguments)
            };
            o._ = [];
            s = d.getElementsByTagName('script')[0];
            c = d.createElement('script');
            c.type = 'text/javascript';
            c.charset = 'utf-8';
            c.async = true;
            c.src = 'https://www.smartsuppchat.com/loader.js?';
            s.parentNode.insertBefore(c, s);
        })(document);
        @endif
    </script>

    @yield('styles')
</head>
