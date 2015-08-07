var elixir = require('laravel-elixir');

elixir.config.assetsDir = './Resources/Assets/';

elixir(function(mix) {
    mix.sass('app.scss', './Public/Assets/css/bundle.css')
        .browserify('app.js', './Public/Assets/js/bundle.js')
});