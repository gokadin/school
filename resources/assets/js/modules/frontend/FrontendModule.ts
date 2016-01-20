import {Component} from 'angular2/core';
import {ROUTER_DIRECTIVES, RouteConfig} from 'angular2/router';

import {IndexPage} from './../../pages/frontend/index/IndexPage';
import {LoginPage} from './../../pages/frontend/login/LoginPage';

@Component({
    selector: 'frontend-module',
    directives: [ROUTER_DIRECTIVES],
    template: require('./frontendModule.html')
})
@RouteConfig([
    { path: '/', name: 'Index', component: IndexPage, useAsDefault: true },
    { path: '/login', name: 'Login', component: LoginPage},
])
export class FrontendModule {

}