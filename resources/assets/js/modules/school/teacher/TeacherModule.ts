import {Component} from 'angular2/core';
import {ROUTER_DIRECTIVES, RouteConfig, Router} from 'angular2/router';

import {User} from './../../../models/user';

import {AuthService} from "../../../services/authService";

import {IndexModule} from "./index/IndexModule";
import {ActivityModule} from "./activity/ActivityModule";

@Component({
    selector: 'teacher-module',
    directives: [ROUTER_DIRECTIVES],
    template: require('./teacherModule.html')
})
@RouteConfig([
    { path: '/...', name: 'Index', component: IndexModule, useAsDefault: true },
    { path: '/activities/...', name: 'Activity', component: ActivityModule}
])
export class TeacherModule {
    subMenus: Object;
    showResponsiveMenu: boolean = false;
    currentUser: User;

    constructor(private router: Router, authService: AuthService) {
        this.initializeSubMenuStates();

        authService.user.subscribe(
            (user: User) => this.currentUser = user
        );
    }

    initializeSubMenuStates() {
        this.subMenus = {
            activities: false,
            students: false,
            settings: false
        };
    }

    toggleSubMenu(subMenu: string) {
        this.subMenus[subMenu] = !this.subMenus[subMenu];
    }

    toggleResponsiveMenu() {
        this.showResponsiveMenu = !this.showResponsiveMenu;
        console.log(this.showResponsiveMenu);
    }

    logout() {
        localStorage.removeItem('authToken');
        window.location.replace('/home/login');
    }
}