import {Component} from 'angular2/core';
import {ROUTER_DIRECTIVES, RouteConfig} from 'angular2/router';

import {Index} from "./index/index/index";

@Component({
    selector: 'teacher',
    directives: [ROUTER_DIRECTIVES],
    template: require('./teacher.html')
})
@RouteConfig([
    { path: '/', name: 'Index', component: Index, useAsDefault: true }
])
export class Teacher {
    logout() {
        localStorage.removeItem('authToken');
        window.location.replace('/home/login');
    }
}