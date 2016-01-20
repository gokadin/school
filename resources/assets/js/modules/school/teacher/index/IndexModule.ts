import {Component} from 'angular2/core';
import {ROUTER_DIRECTIVES, RouteConfig, Router} from 'angular2/router';

import {IndexPage} from "../../../../pages/school/teacher/index/index/IndexPage";

@Component({
    selector: 'index-module',
    directives: [ROUTER_DIRECTIVES],
    template: require('./indexModule.html')
})
@RouteConfig([
    { path: '/', name: 'Index', component: IndexPage, useAsDefault: true }
])
export class IndexModule {

}