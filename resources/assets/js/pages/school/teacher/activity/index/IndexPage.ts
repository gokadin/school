import {Component} from 'angular2/core';

import {ActivityList} from "../../../../../components/school/teacher/activityList/ActivityList";

@Component({
    selector: 'index-page',
    directives: [ActivityList],
    template: require('./indexPage.html')
})
export class IndexPage {

}