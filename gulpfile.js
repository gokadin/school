var elixir = require('laravel-elixir');

<<<<<<< HEAD
elixir(function(mix) {
    mix.sass('app.scss')
        .browserify('app.js')
=======
elixir.config.assetsDir = './resources/assets/';

elixir(function(mix) {
    mix.sass('app.scss', './public/assets/css/bundle.css')
        .browserify('app.js', './public/assets/js/bundle.js')
>>>>>>> before_restart
});