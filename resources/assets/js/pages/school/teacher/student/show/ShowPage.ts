import {Component} from 'angular2/core';
import {RouteParams} from 'angular2/router';

import {StudentProfile} from "../../../../../components/school/teacher/studentProfile/StudentProfile";
import {StudentSwitcher} from "../studentSwitcher/StudentSwitcher";

@Component({
    selector: 'show-page',
    directives: [StudentSwitcher, StudentProfile],
    template: require('./showPage.html')
})
export class ShowPage {
    studentId: number;

    constructor(params: RouteParams) {
        this.studentId = params.get('id');
    }
}