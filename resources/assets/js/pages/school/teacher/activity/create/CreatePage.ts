import {Component} from 'angular2/core';

import {CreateActivityForm} from "../../../../../components/school/teacher/createActivityForm/CreateActivityForm";

@Component({
    selector: 'create-page',
    directives: [CreateActivityForm],
    template: require('./createPage.html')
})
export class CreatePage {

}