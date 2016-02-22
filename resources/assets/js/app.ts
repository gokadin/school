import {Component, provide} from 'angular2/core';
import {bootstrap} from 'angular2/platform/browser';
import {ROUTER_DIRECTIVES, ROUTER_PROVIDERS, LocationStrategy, RouteConfig} from 'angular2/router';
import {HTTP_PROVIDERS, BaseRequestOptions, RequestOptions, Headers} from 'angular2/http';

require('font-awesome-webpack');
require('./../sass/app.scss');
require('./components/modal/modal.scss');

import {AppRequestOptions} from './requests/appRequestOptions';

import {AUTH_PROVIDERS} from './services/authService';
import {EVENT_PROVIDERS} from './services/eventService';
import {ACTIVITY_PROVIDERS} from './services/ActivityService';
import {STUDENT_PROVIDERS} from './services/StudentService';
import {FLASH_PROVIDERS} from './components/flash/Flash';

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
    ACTIVITY_PROVIDERS,
    STUDENT_PROVIDERS,
    FLASH_PROVIDERS,
    provide(RequestOptions, {useClass: AppRequestOptions})
]);