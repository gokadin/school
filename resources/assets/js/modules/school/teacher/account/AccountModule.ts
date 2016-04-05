import {Component} from 'angular2/core';
import {ROUTER_DIRECTIVES, RouteConfig, Router} from 'angular2/router';

import {ProfilePage} from "../../../../pages/school/teacher/account/profile/ProfilePage";

@Component({
    selector: 'account-module',
    directives: [ROUTER_DIRECTIVES],
    template: require('./accountModule.html')
})
@RouteConfig([
    { path: '/', name: 'Profile', component: ProfilePage, useAsDefault: true },
])
export class AccountModule {

}