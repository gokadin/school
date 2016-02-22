import {Component} from 'angular2/core';
import {ROUTER_DIRECTIVES, RouteConfig, Router} from 'angular2/router';

import {IndexPage} from "../../../../pages/school/teacher/student/index/IndexPage";

@Component({
    selector: 'student-module',
    directives: [ROUTER_DIRECTIVES],
    template: require('./studentModule.html')
})
@RouteConfig([
    { path: '/', name: 'Index', component: IndexPage, useAsDefault: true }
])
export class StudentModule {

}