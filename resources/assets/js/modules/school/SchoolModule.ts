import {Component, Injector} from 'angular2/core';
import {bootstrap} from 'angular2/platform/browser';
import {ROUTER_DIRECTIVES, RouteConfig, CanActivate} from 'angular2/router';
import {HTTP_PROVIDERS} from 'angular2/http';

import {AuthService} from '../../services/authService';

import {TeacherModule} from "./teacher/TeacherModule";
import {Flash} from "../../components/flash/Flash";

@Component({
    selector: 'school-module',
    directives: [ROUTER_DIRECTIVES, Flash],
    template: require('./schoolModule.html')
})
@CanActivate(
    () => {
        return localStorage.getItem('authToken') ? true : false;
    }
)
@RouteConfig([
    { path: '/teacher/...', name: 'Teacher', component: TeacherModule, useAsDefault: true}
])
export class SchoolModule {

}