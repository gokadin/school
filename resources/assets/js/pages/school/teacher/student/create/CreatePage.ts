import {Component} from 'angular2/core';

import {CreateStudentForm} from "../../../../../components/school/teacher/createStudentForm/CreateStudentForm";
import {InfoBox} from "../../../../../components/school/infoBox/InfoBox";

@Component({
    selector: 'create-page',
    directives: [CreateStudentForm, InfoBox],
    template: require('./createPage.html')
})
export class CreatePage {

}