import {Component} from 'angular2/core';

import {StudentList} from "../../../../../components/school/teacher/studentList/StudentList";
import {PendingStudentList} from "../../../../../components/school/teacher/pendingStudentList/PendingStudentList";

@Component({
    selector: 'index-page',
    directives: [StudentList, PendingStudentList],
    template: require('./indexPage.html')
})
export class IndexPage {

}