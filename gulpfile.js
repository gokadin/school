var elixir = require('laravel-elixir');

elixir(function(mix) {
    mix.browserify('app.js', './Public/Assets/js/bundle.js');
});