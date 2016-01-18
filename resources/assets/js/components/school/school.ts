import {Component, Injector} from 'angular2/core';
import {bootstrap} from 'angular2/platform/browser';
import {ROUTER_DIRECTIVES, RouteConfig, CanActivate} from 'angular2/router';

import {AuthService} from '../../services/authService';

import {Teacher} from "./teacher/teacher";

@Component({
    selector: 'school',
    directives: [ROUTER_DIRECTIVES],
    template: require('./school.html')
})
@CanActivate(
    () => {
        let injector: any = Injector.resolveAndCreate([AuthService]);
        let authService: AuthService = injector.get(AuthService);

        return authService.isLoggedIn();
    }
)
@RouteConfig([
    { path: '/teacher/...', name: 'Teacher', component: Teacher, useAsDefault: true}
])
export class School {

}