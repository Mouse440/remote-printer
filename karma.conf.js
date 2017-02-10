// Karma configuration
// Generated on Mon Feb 06 2017 20:52:31 GMT-0800 (PST)

module.exports = function(config) {
  config.set({

    // base path that will be used to resolve all patterns (eg. files, exclude)
    basePath: '',


    // frameworks to use
    // available frameworks: https://npmjs.org/browse/keyword/karma-adapter
    frameworks: ['jasmine'],


    // list of files / patterns to load in the browser
    files: [
        './node_modules/angular/angular.js',
        './node_modules/angular-mocks/angular-mocks.js',
        './node_modules/ng-file-upload/dist/ng-file-upload-all.min.js',
        './node_modules/ng-file-upload/dist/ng-file-upload.min.js',

        './app/app.js',
        './app/shared/endpoints/endpoints.value.js',
        './app/shared/endpoints/endpoints.request.service.js',
        './app/shared/options.utilities.service.js',
        './app/components/print_options_form/pluralize.filter.js',
        './app/components/print_options_form/print.allowance.color.filter.js',
        './app/components/print_options_form/print.options.controller.js',
        './app/components/copies_panel/copies.panel.directive.js',
        './app/components/copies_panel/copies.panel.controller.js',
        './app/components/pagerange_panel/pagerange.panel.directive.js',
        './app/components/pagerange_panel/pagerange.input.directive.js',
        './app/components/pagerange_panel/pagerange.controller.js',
        './app/components/preview/print.previewer.directive.js',
        './app/components/upload/upload.controller.js',
        './app/components/app/app.controller.js',
        './app/app.module.js',

        './app/components/pagerange_panel/pagerange.input.spec.js',
        './app/components/pagerange_panel/pagerange.controller.spec.js'
    ],


    // list of files to exclude
    exclude: [
    ],


    // preprocess matching files before serving them to the browser
    // available preprocessors: https://npmjs.org/browse/keyword/karma-preprocessor
    preprocessors: {
    },


    // test results reporter to use
    // possible values: 'dots', 'progress'
    // available reporters: https://npmjs.org/browse/keyword/karma-reporter
    reporters: ['spec'],


    // web server port
    port: 9876,


    // enable / disable colors in the output (reporters and logs)
    colors: true,


    // level of logging
    // possible values: config.LOG_DISABLE || config.LOG_ERROR || config.LOG_WARN || config.LOG_INFO || config.LOG_DEBUG
    logLevel: config.LOG_INFO,


    // enable / disable watching file and executing tests whenever any file changes
    autoWatch: true,


    // start these browsers
    // available browser launchers: https://npmjs.org/browse/keyword/karma-launcher
    browsers: ['Chrome'],


    // Continuous Integration mode
    // if true, Karma captures browsers, runs the tests and exits
    singleRun: false,

    // Concurrency level
    // how many browser should be started simultaneous
    concurrency: Infinity
  })
}
