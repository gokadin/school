import {Component, provide} from 'angular2/core';
import {bootstrap} from 'angular2/platform/browser';
import {ROUTER_DIRECTIVES, ROUTER_PROVIDERS,
    HashLocationStrategy, LocationStrategy, RouteConfig} from 'angular2/router';
import {HTTP_PROVIDERS, BaseRequestOptions, RequestOptions, Headers} from 'angular2/http';

require('./../sass/app.scss');

import {AppRequestOptions} from './requests/appRequestOptions';

import {AUTH_PROVIDERS} from './services/authService';

import {School} from './components/school/school';
import {Frontend} from './components/frontend/frontend';

@Component({
    selector: 'app',
    directives: [ROUTER_DIRECTIVES],
    template: require('./app.html')
})
@RouteConfig([
    { path: '/home/...', name: 'Frontend', component: Frontend, useAsDefault: true},
    { path: '/school/...', name: 'School', component: School}
])
class App {

}

bootstrap(App, [
    ROUTER_PROVIDERS,
    HTTP_PROVIDERS,
    AUTH_PROVIDERS,
    provide(RequestOptions, {useClass: AppRequestOptions})
]);