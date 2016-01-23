import {Component} from 'angular2/core';
import {ROUTER_DIRECTIVES, RouteConfig, Router} from 'angular2/router';

import {IndexPage} from "../../../../pages/school/teacher/calendar/index/IndexPage";

@Component({
    selector: 'calendar-module',
    directives: [ROUTER_DIRECTIVES],
    template: require('./calendarModule.html')
})
@RouteConfig([
    { path: '/', name: 'Index', component: IndexPage, useAsDefault: true }
])
export class CalendarModule {

}