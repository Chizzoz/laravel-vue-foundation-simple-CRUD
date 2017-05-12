const { mix } = require('laravel-mix');

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

mix.sass('resources/assets/sass/app.scss', 'public/css')
   .js('resources/assets/js/app.js', 'public/js');
mix.copy('vendor/components/jquery/jquery.min.js', 'public/js');
mix.copy('vendor/zurb/foundation/dist/js/foundation.min.js', 'public/js');
mix.copy('vendor/grimmlink/toastr/build/toastr.min.css', 'public/css');
mix.copy('vendor/grimmlink/toastr/build/toastr.min.js', 'public/js');