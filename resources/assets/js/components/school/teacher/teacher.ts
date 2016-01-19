import {Component} from 'angular2/core';
import {ROUTER_DIRECTIVES, RouteConfig, Router} from 'angular2/router';

import {User} from './../../../models/user';

import {AuthService} from "../../../services/authService";

import {Index} from "./index/index/index";
import {ActivityList} from "./activities/activityList/activityList";
import {CreateActivity} from "./activities/createActivity/createActivity";

@Component({
    selector: 'teacher',
    directives: [ROUTER_DIRECTIVES],
    template: require('./teacher.html')
})
@RouteConfig([
    { path: '/', name: 'Index', component: Index, useAsDefault: true },
    { path: '/activities', name: 'ActivityList', component: ActivityList},
    { path: '/activities/create', name: 'CreateActivity', component: CreateActivity}
])
export class Teacher {
    subMenus: Object;
    currentUser: User;

    constructor(private router: Router, authService: AuthService) {
        this.subMenus = {activities: false, students: false, settings: false};

        authService.user.subscribe(
            (user: User) => this.currentUser = user
        );
    }

    toggleSubMenu(subMenu: string) {
        this.subMenus[subMenu] = !this.subMenus[subMenu];
    }

    logout() {
        localStorage.removeItem('authToken');
        window.location.replace('/home/login');
    }
}