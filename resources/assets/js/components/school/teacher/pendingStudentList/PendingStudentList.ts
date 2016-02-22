import {Component} from 'angular2/core';

import {StudentService} from "../../../../services/StudentService";
import {ApiComponent} from "../../../ApiComponent";

require('./pendingStudentList.scss');

@Component({
    selector: 'pending-student-list',
    template: require('./pendingStudentList.html')
})
export class PendingStudentList extends ApiComponent {
    open: boolean = true;

    constructor(studentService: StudentService) {
        this.subscribeToSource(studentService.pending);
        studentService.fetchPending();
    }
}