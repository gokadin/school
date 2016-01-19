var webpack = require('webpack');
var path = require('path');

module.exports = {
  entry: {
    vendor: path.resolve(__dirname, 'resources/assets/js/vendor.ts'),
    bundle: [path.resolve(__dirname, 'resources/assets/js/app.ts')]
  },
  output: {
    path: path.resolve(__dirname, 'public/js'),
    filename: 'bundle.js'
  },
  resolve: {
    extensions: ['', '.webpack.js', '.web.js', '.ts', '.js']
  },
  module: {
    loaders: [
      {test: /\.ts$/, loader: 'ts-loader'},
      {test: /\.css$/, loader: 'style!css'},
      {test: /\.scss$/, loader: 'style!css!sass'},
      {test: /\.less$/, loader: "style!css!less"},
      {test: /\.html$/, loader: 'raw'},
      {test: /\.woff(2)?(\?v=[0-9]\.[0-9]\.[0-9])?$/, loader: "url-loader?limit=10000&minetype=application/font-woff"},
      {test: /\.(ttf|eot|svg)(\?v=[0-9]\.[0-9]\.[0-9])?$/, loader: "file-loader"}
    ],
    noParse: [
      /\.min\.js/,
      /vendor\/.*?\.(js|css)$/
    ]
  },
  plugins: [
    new webpack.optimize.CommonsChunkPlugin('vendor', 'vendor.js')
  ]
}