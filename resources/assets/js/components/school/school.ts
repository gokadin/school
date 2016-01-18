import {Component, Injector} from 'angular2/core';
import {ROUTER_DIRECTIVES, RouteConfig, CanActivate} from 'angular2/router';

import {AuthService} from '../../services/authService';

import {StudentList} from "./messaging/studentList/studentList";

@Component({
    selector: 'school',
    directives: [ROUTER_DIRECTIVES],
    template: require('./school.html')
})
@CanActivate(
    (next: any, curr: any) => {
        let injector: any = Injector.resolveAndCreate([AuthService]);
        let authService: AuthService = injector.get(AuthService);

        return authService.isLoggedIn();
    }
)
@RouteConfig([
    { path: '/messaging', name: 'StudentList', component: StudentList, useAsDefault: true}
])
export class School {

}