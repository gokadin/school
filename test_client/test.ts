import 'es6-shim';
import 'es6-promise';
import 'es7-reflect-metadata/dist/browser';
import 'zone.js/dist/zone';
import 'rxjs';

var testsContext = require.context(".", true, /.spec.ts$/);
testsContext.keys().forEach(testsContext);