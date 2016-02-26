import {Component} from 'angular2/core';
import {RouteParams} from 'angular2/router';

import {Switcher} from "../../../../../components/switcher/Switcher";
import {StudentProfile} from "../../../../../components/school/teacher/studentProfile/StudentProfile";

@Component({
    selector: 'show-page',
    directives: [Switcher, StudentProfile],
    template: require('./showPage.html')
})
export class ShowPage {
    studentId: number;
    switcherLinks: Array<Object>;

    constructor(params: RouteParams) {
        this.studentId = params.get('id');

        this.switcherLinks = [
            {route: ['/School/Teacher/Student/Show', {id: this.studentId}], name: 'Profile', icon: 'fa-user'},
            {route: ['/School/Teacher/Student/Show', {id: this.studentId}], name: 'Activities', icon: 'fa-cogs'},
            {route: ['/School/Teacher/Student/Show', {id: this.studentId}], name: 'Lessons', icon: 'fa-book'}
        ];
    }
}