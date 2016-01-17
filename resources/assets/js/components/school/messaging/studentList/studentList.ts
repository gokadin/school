import {Component} from 'angular2/core';
import {Http, Response} from 'angular2/http';

@Component({
    selector: 'student-list',
    template: require('./studentList.html')
})
export class StudentList {
    students: Object;
    loading: boolean;

    constructor(public http: Http) {
        this.makeRequest();
    }

    makeRequest(): void {
        this.loading = true;
        this.http.get('/test/api/school/teacher/messaging/students')
            .subscribe((res: Response) => {
                this.students = res.json().students;
                this.loading = false;
            });
    }
}