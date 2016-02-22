import {Component} from 'angular2/core';
import {Http, Response} from 'angular2/http';
import {Router, ROUTER_DIRECTIVES} from 'angular2/router';
import {Control, AbstractControl} from 'angular2/common';

import {StudentService} from "../../../../services/StudentService";

@Component({
    selector: 'student-list',
    directives: [ROUTER_DIRECTIVES],
    template: require('./studentList.html')
})
export class StudentList {
    isLoading: boolean;
    initialDataSize: number;
    hasError: boolean;
    data: any;
    page: number;
    max: number;
    total: number;
    searchRules: Object;

    constructor(private studentService: StudentService) {
        this.isLoading = true;
        this.initialDataSize = 0;
        this.page = 0;
        this.max = 10;
        this.total = 0;
        this.searchRules = {
            firstName: new Control(),
            lastName: new Control(),
            email: new Control()
        }

        studentService.paginated
            .subscribe(
                (data: any) => {
                    this.isLoading = false;
                    this.data = data.students;
                    this.page = data.pagination.pageNumber;
                    this.total = data.pagination.totalCount;
                    if (this.initialDataSize == 0) {
                        this.initialDataSize = this.data.length;
                    }
                },
                () => {
                    this.hasError = true;
                }
            );

        this.fetchStudents();

        for (let key in this.searchRules) {
            this.searchRules[key].valueChanges
                .debounceTime(400)
                .distinctUntilChanged()
                .subscribe(
                    () => {
                        this.page = 0;
                        this.fetchStudents();
                    }
                );
        }
    }

    fetchStudents(): void {
        this.studentService.paginate(this.page, this.max, this.searchRules);
    }

    nextPage(): void {
        if ((this.page + 1) * this.max >= this.total) {
            return;
        }

        this.page++;
        this.fetchStudents();
    }

    previousPage(): void {
        if (this.page == 0) {
            return;
        }

        this.page--;
        this.fetchStudents();
    }
}