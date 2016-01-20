import {Component} from 'angular2/core';

import {InfoBox} from "../../../../../components/school/infoBox/InfoBox";
import {CreateActivityForm} from "../../../../../components/school/teacher/createActivityForm/CreateActivityForm";

@Component({
    selector: 'create-page',
    directives: [InfoBox, CreateActivityForm],
    template: require('./createPage.html')
})
export class CreatePage {

}