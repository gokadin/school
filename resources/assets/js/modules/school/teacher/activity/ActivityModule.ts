import {Component} from 'angular2/core';
import {ROUTER_DIRECTIVES, RouteConfig, Router} from 'angular2/router';

import {IndexPage} from "../../../../pages/school/teacher/activity/index/IndexPage";
import {CreatePage} from "../../../../pages/school/teacher/activity/create/CreatePage";

@Component({
    selector: 'activity-module',
    directives: [ROUTER_DIRECTIVES],
    template: require('./activityModule.html')
})
@RouteConfig([
    { path: '/', name: 'Index', component: IndexPage, useAsDefault: true },
    { path: '/create', name: 'Create', component: CreatePage }
])
export class ActivityModule {

}