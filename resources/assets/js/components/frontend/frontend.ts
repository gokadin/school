import {Component} from 'angular2/core';
import {ROUTER_DIRECTIVES, RouteConfig} from 'angular2/router';

import {Home} from './index/home/home';
import {Login} from './account/login/login';

@Component({
    selector: 'frontend',
    directives: [ROUTER_DIRECTIVES],
    template: require('./frontend.html')
})
@RouteConfig([
    { path: '/', name: 'Home', component: Home, useAsDefault: true },
    { path: '/login', name: 'Login', component: Login},
])
export class Frontend {

}