// Karma configuration
// Generated on Sun Apr 10 2016 02:56:22 GMT-0400 (EDT)

var path = require('path');
var cwd = process.cwd();

module.exports = function(config) {
  config.set({

    // base path that will be used to resolve all patterns (eg. files, exclude)
    basePath: '',


    // frameworks to use
    // available frameworks: https://npmjs.org/browse/keyword/karma-adapter
    frameworks: ['jasmine'],


    // list of files / patterns to load in the browser
    files: [
      'test_client/test.ts'
    ],

    // preprocess matching files before serving them to the browser
    // available preprocessors: https://npmjs.org/browse/keyword/karma-preprocessor
    preprocessors: {
      'test_client/test.ts': ['webpack']
    },

    webpack: {
      resolve: {
        root: [path.resolve(cwd)],
        modulesDirectories: ['node_modules', 'test_client', '.', 'resources/assets/js'],
        extensions: ['', '.ts', '.spec.ts', '.js', '.css'],
        alias: {
          'app': 'app'
        }
      },
      module: {
        loaders: [
          {test: /\.ts$/, loader: 'ts-loader', exclude: [/node_modules/]},
          {test: /\.css$/, loader: 'style!css'},
          {test: /\.scss$/, loader: 'style!css!sass'},
          {test: /\.html$/, loader: 'raw'}
        ]
      },
      stats: {
        colors: true,
        reasons: true
      },
      watch: true,
      debug: true
    },

    webpackServer: {
      noInfo: true
    },


    // test results reporter to use
    // possible values: 'dots', 'progress'
    // available reporters: https://npmjs.org/browse/keyword/karma-reporter
    reporters: ['progress'],


    // web server port
    port: 9876,


    // enable / disable colors in the output (reporters and logs)
    colors: true,


    // level of logging
    // possible values: config.LOG_DISABLE || config.LOG_ERROR || config.LOG_WARN || config.LOG_INFO || config.LOG_DEBUG
    logLevel: config.LOG_INFO,


    // enable / disable watching file and executing tests whenever any file changes
    autoWatch: false,


    // start these browsers
    // available browser launchers: https://npmjs.org/browse/keyword/karma-launcher
    browsers: ['PhantomJS'],


    // Continuous Integration mode
    // if true, Karma captures browsers, runs the tests and exits
    singleRun: true,

    plugins: [
      'karma-teamcity-reporter',
      'karma-jasmine',
      'karma-webpack',
      'karma-phantomjs-launcher'
    ],
  })
}
