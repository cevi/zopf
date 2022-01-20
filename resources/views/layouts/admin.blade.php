<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="description" content="Zopfaktion">
      <meta name="author" content="Jérôme Sigg">
      <meta name="robots" content="all,follow">

      <title>Zopfaktion Zentrale</title>

      <!-- Bootstrap Core CSS -->
      <link href="{{asset('css/libs.css')}}" rel="stylesheet">
      @livewireStyles
      @yield('styles')
  </head>

  <body>
      @include('includes/admin_sidenav')
      
      @include('includes/admin_topnav')

      @yield('content')
      <footer class="main-footer">
          <div class="container-fluid">
            <div class="row">
              <div class="col-sm-6">
                <p>Amigo &copy; 2020</p>
              </div>
              <div class="col-sm-6 text-right">
                <p>Version 1.0.0</p>
              </div>
            </div>
          </div>
      </footer>
  </div>
        

        <!-- jQuery -->
        <script src="{{asset('js/libs.js')}}"></script>
        <script src="https://unpkg.com/echarts/dist/echarts.min.js"></script>
        <script src="https://unpkg.com/@chartisan/echarts/dist/chartisan_echarts.js"></script>
        @yield('scripts')
        @livewireScripts
    </body>

</html>