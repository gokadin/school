import {Component, provide} from 'angular2/core';
import {bootstrap} from 'angular2/platform/browser';
import {ROUTER_DIRECTIVES, ROUTER_PROVIDERS,
    HashLocationStrategy, LocationStrategy, RouteConfig} from 'angular2/router';
import {HTTP_PROVIDERS, BaseRequestOptions, RequestOptions, Headers} from 'angular2/http';

require('./../sass/app.scss');

import {AUTH_PROVIDERS} from './services/authService';

import {School} from './components/school/school';
import {Home} from './components/frontend/home/home';
import {Login} from './components/frontend/account/login/login';
import {StudentList} from './components/school/messaging/studentList/studentList';

@Component({
    selector: 'app',
    directives: [ROUTER_DIRECTIVES],
    template: require('./app.html')
})
@RouteConfig([
    { path: '/', name: 'Home', component: Home, useAsDefault: true },
    { path: '/login', name: 'Login', component: Login},
    { path: '/school/...', name: 'School', component: School}
])
class App {
    logout() {
        localStorage.removeItem('authToken');
    }
}

class MyOptions extends RequestOptions {
        constructor() {
            super({
                headers: new Headers({
                    'Content-Type': 'text/json; charset=UTF-8',
                    'CSRFTOKEN': document.getElementById('csrf-token').getAttribute('content')
                })
            });
        }
}

bootstrap(App, [
    ROUTER_PROVIDERS,
    HTTP_PROVIDERS,
    AUTH_PROVIDERS,
    provide(RequestOptions, {useClass: MyOptions})
]);