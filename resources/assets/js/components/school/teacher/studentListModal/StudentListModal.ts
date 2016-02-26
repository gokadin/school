import {Component} from 'angular2/core';
import {ROUTER_DIRECTIVES} from 'angular2/router';

import {Modal} from "../../../modal/Modal";
import {Student} from "../../../../models/Student";

require('./studentListModal.scss');

@Component({
    selector: 'student-list-modal',
    directives: [ROUTER_DIRECTIVES],
    template: require('./studentListModal.html')
})
export class StudentListModal extends Modal{
    loading: boolean;
    students: Student[];

    constructor() {
        this.loading = true;
    }

    prepare(students: Student[]): void {
        this.students = students;
    }

    setLoading(): void {
        this.loading = true;
    }

    setStudents(students: Student[]): void {
        this.students = students;
        this.loading = false;
    }
}