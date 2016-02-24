import {Component} from 'angular2/core';
import {ROUTER_DIRECTIVES, RouteConfig, Router} from 'angular2/router';

import {IndexPage} from "../../../../pages/school/teacher/student/index/IndexPage";
import {CreatePage} from "../../../../pages/school/teacher/student/create/CreatePage";

@Component({
    selector: 'student-module',
    directives: [ROUTER_DIRECTIVES],
    template: require('./studentModule.html')
})
@RouteConfig([
    { path: '/', name: 'Index', component: IndexPage, useAsDefault: true },
    { path: '/create', name: 'Create', component: CreatePage}
])
export class StudentModule {

}