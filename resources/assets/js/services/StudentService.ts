import {Injectable, provide} from 'angular2/core';
import {Http, Response} from 'angular2/http';
import {Observable, Subject} from 'rxjs';
import moment = require("moment");

import {Student} from "../models/Student";
import {TempStudent} from "../models/TempStudent";
import {Lesson} from "../models/Lesson";

@Injectable()
export class StudentService {
    paginated: Subject<Object>;
    pending: Subject<Object>;
    profile: Subject<Object>;
    lessons: Subject<Object>;

    constructor(private http: Http) {
        this.paginated = new Subject<Object>();
        this.pending = new Subject<Object>();
        this.profile = new Subject<Object>();
        this.lessons = new Subject<Object>();
    }

    fetchLessons(id: number): void {
        let from = moment().subtract(1, 'months').format('YYYY-MM-DD');
        let to = moment().format('YYYY-MM-DD');

        this.http.get('/api/school/teacher/students/' + id + '/lessons?from=' + from + '&to=' + to)
            .map(data => data.json())
            //.map(data => data.lessons
            //    .map(lesson => new Lesson(lesson)))
            .subscribe(
                data => this.lessons.next(data.lessons)
            );
    }

    fetchProfile(id: number): void {
        this.http.get('/api/school/teacher/students/' + id)
            .map((data: Response) => data.json())
            .map(data => {
                data.student = new Student(data.student);
                return data;
            })
            .subscribe(
                (data: Object) => this.profile.next(data)
            );
    }

    fetchPending(): void {
        this.http.get('/api/school/teacher/students/pending')
            .map((data: Response) => data.json())
            .map((data: any) => {
                return {
                    students: data.students
                        .map((student: Object) => new TempStudent(student))
                }
            })
            .subscribe(
                (data: Object) => this.pending.next(data.students)
            );
    }

    paginate(page: number, max: number, searchRules: Object = {}, sortingRules = {}): void {
        this.http.get('/api/school/teacher/students/paginate?page=' + page + '&max=' +
                max + this.buildSearchParams(searchRules) + this.buildSortParams(sortingRules))
            .map((data: Response) => data.json())
            .map((data: any) => {
                return {
                    pagination: data.pagination,
                    students: data.students
                        .map((student: Object) => new Student(student))
                }
            })
            .subscribe(
                (data: Object) => this.paginated.next(data)
            );
    }

    buildSearchParams(searchRules: Object): string {
        let str = '';

        for (let key in searchRules) {
            if (!searchRules[key].value) {
                continue;
            }

            str += '&search[' + key + ']=' + searchRules[key].value;
        }

        return str;
    }

    buildSortParams(sortingRules: Object): string {
        let str = '';

        for (let key in sortingRules) {
            if (!sortingRules[key].value) {
                continue;
            }

            str += '&sort[' + key + ']=' + sortingRules[key].value;
        }

        return str;
    }
}

export var STUDENT_PROVIDERS = [
    provide(StudentService, {useClass: StudentService})
];