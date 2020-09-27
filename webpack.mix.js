const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
   .sass('resources/sass/app.scss', 'public/css');

   mix.styles([
      'resources/css/libs/jquery-ui.css',
      'resources/css/libs/bootstrap.css',
      'resources/css/libs/sb-admin-2.css',
      'resources/css/libs/datatables.css',
      'resources/css/libs/font-awesome_new.css',
      'resources/css/libs/fontastic.css',
      'resources/css/libs/map-icons.css',
      'resources/css/libs/grasp_mobile_progress_circle-1.0.0.css',
      'resources/css/libs/jquery.mCustomScrollbar.css',
      'resources/css/libs/style.default.premium.css',
      'resources/css/libs/dropify.min.css',
      'resources/css/libs/custom.css',
  ], 'public/css/libs.css');
  mix.scripts([
   'resources/js/libs/jquery.js',
   'resources/js/libs/jquery-ui.js',
   'resources/js/libs/dropify.min.js',
   'resources/js/libs/bootstrap.js',
   'resources/js/libs/grasp_mobile_progress_circle-1.0.0.min.js',
   'resources/js/libs/chart.min.js',
   'resources/js/libs/datatables.js',
   'resources/js/libs/jquery.mCustomScrollbar.js',
   'resources/js/libs/front_new.js',
   'resources/js/libs/map-icons.js',
   'resources/js/libs/markerCluster.js',
   'resources/js/libs/front.js',
   'resources/js/libs/custom.js',
], 'public/js/libs.js');  
