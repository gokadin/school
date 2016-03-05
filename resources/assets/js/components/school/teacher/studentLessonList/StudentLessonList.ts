import {Component, Input} from 'angular2/core';

import {StudentService} from "../../../../services/StudentService";
import {ApiComponent} from "../../../ApiComponent";

@Component({
    selector: 'student-lesson-list',
    template: require('./studentLessonList.html')
})
export class StudentLessonList extends ApiComponent {
    @Input() studentId: number;

    constructor(private studentService: StudentService) {
        this.subscribeToSource(this.studentService.lessons);
    }

    ngAfterViewInit() {
        this.studentService.fetchLessons(this.studentId);
    }
}