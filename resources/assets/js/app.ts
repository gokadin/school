import {Component, provide} from 'angular2/core';
import {bootstrap} from 'angular2/platform/browser';
import {ROUTER_DIRECTIVES, ROUTER_PROVIDERS,
    HashLocationStrategy, LocationStrategy, RouteConfig} from 'angular2/router';
import {HTTP_PROVIDERS, BaseRequestOptions} from 'angular2/http';

require('./../sass/app.scss');

import {AUTH_PROVIDERS} from './services/authService';

import {Home} from './components/school/home/home';
import {Login} from "./components/frontend/account/login/login";
import {StudentList} from './components/school/messaging/studentList/studentList';

@Component({
    selector: 'app',
    directives: [ROUTER_DIRECTIVES],
    template: require('./app.html')
})
@RouteConfig([
    { path: '/', name: 'Home', component: Home },
    { path: '/login', name: 'Login', component: Login},
    { path: '/test/school/teacher/messaging/', name: 'StudentList', component: StudentList}
])
class App {

}

bootstrap(App, [
    ROUTER_PROVIDERS,
    HTTP_PROVIDERS,
    AUTH_PROVIDERS
]);