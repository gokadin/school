var elixir = require('laravel-elixir');

elixir.config.assetsDir = './resources/assets/';

elixir(function(mix) {
    mix.sass('app.scss', './public/assets/css/bundle.css')
        .browserify('app.js', './public/assets/js/bundle.js')
});