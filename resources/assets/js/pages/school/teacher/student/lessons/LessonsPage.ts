import {Component} from 'angular2/core';
import {RouteParams} from 'angular2/router';

import {StudentSwitcher} from "../studentSwitcher/StudentSwitcher";
import {StudentLessonList} from "../../../../../components/school/teacher/studentLessonList/StudentLessonList";

@Component({
    selector: 'lessons-page',
    directives: [StudentSwitcher, StudentLessonList],
    template: require('./lessonsPage.html')
})
export class LessonsPage {
    studentId: number;

    constructor(params: RouteParams) {
        this.studentId = params.get('id');
    }
}