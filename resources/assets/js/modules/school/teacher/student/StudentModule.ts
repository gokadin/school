import {Component} from 'angular2/core';
import {ROUTER_DIRECTIVES, RouteConfig, Router} from 'angular2/router';

import {IndexPage} from "../../../../pages/school/teacher/student/index/IndexPage";
import {CreatePage} from "../../../../pages/school/teacher/student/create/CreatePage";
import {ShowPage} from "../../../../pages/school/teacher/student/show/ShowPage";
import {LessonsPage} from "../../../../pages/school/teacher/student/lessons/LessonsPage";

@Component({
    selector: 'student-module',
    directives: [ROUTER_DIRECTIVES],
    template: require('./studentModule.html')
})
@RouteConfig([
    { path: '/', name: 'Index', component: IndexPage, useAsDefault: true },
    { path: '/create', name: 'Create', component: CreatePage},
    { path: '/:id', name: 'Show', component: ShowPage},
    { path: '/:id/lessons', name: 'Lessons', component: LessonsPage}
])
export class StudentModule {

}