import 'es6-shim';
import 'es6-promise';
import 'es7-reflect-metadata/dist/browser';
import 'zone.js/dist/zone';
import 'rxjs';

import {BrowserDomAdapter} from 'angular2/src/platform/browser/browser_adapter';
import {TEST_BROWSER_PLATFORM_PROVIDERS, TEST_BROWSER_APPLICATION_PROVIDERS} from 'angular2/platform/testing/browser';
import {setBaseTestProviders} from 'angular2/testing';
setBaseTestProviders(TEST_BROWSER_PLATFORM_PROVIDERS,TEST_BROWSER_APPLICATION_PROVIDERS);
BrowserDomAdapter.makeCurrent();

var testsContext = require.context(".", true, /.spec.ts$/);
testsContext.keys().forEach(testsContext);