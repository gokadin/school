import {Component, provide} from 'angular2/core';
import {bootstrap} from 'angular2/platform/browser';
import {ROUTER_DIRECTIVES, ROUTER_PROVIDERS, LocationStrategy, RouteConfig} from 'angular2/router';
import {HTTP_PROVIDERS, BaseRequestOptions, RequestOptions, Headers} from 'angular2/http';
import moment = require('moment');

require('font-awesome-webpack');
require('./../sass/app.scss');

import {AppRequestOptions} from './requests/appRequestOptions';

import {AUTH_PROVIDERS} from './services/authService';
import {EVENT_PROVIDERS} from './services/eventService';

import {SchoolModule} from './modules/school/SchoolModule';
import {FrontendModule} from './modules/frontend/FrontendModule';

@Component({
    selector: 'app',
    directives: [ROUTER_DIRECTIVES],
    template: require('./app.html')
})
@RouteConfig([
    { path: '/home/...', name: 'Frontend', component: FrontendModule, useAsDefault: true},
    { path: '/school/...', name: 'School', component: SchoolModule}
])
class App {

}

bootstrap(App, [
    ROUTER_PROVIDERS,
    HTTP_PROVIDERS,
    AUTH_PROVIDERS,
    EVENT_PROVIDERS,
    provide(RequestOptions, {useClass: AppRequestOptions})
]);