import {Component, Input} from 'angular2/core';

import {StudentService} from "../../../../services/StudentService";
import {Student} from "../../../../models/Student";

require('./studentProfile.scss');

@Component({
    selector: 'student-profile',
    template: require('./studentProfile.html')
})
export class StudentProfile {
    @Input() studentId: number;
    loading: boolean;
    student: Student;
    profileInformation: Array<Object>;

    constructor(private studentService: StudentService) {
        this.loading = true;
    }

    ngAfterViewInit() {
        this.studentService.profile.subscribe(
            data => {
                this.student = data.student;
                this.profileInformation = data.profileInformation;
                this.loading = false; // do loading and error
            }
        );

        this.studentService.fetchProfile(this.studentId);
    }
}