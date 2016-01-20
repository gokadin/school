import {Component} from 'angular2/core';

import {UpcomingEvents} from "./../../../../../components/school/teacher/upcomingEvents/UpcomingEvents";

@Component({
    selector: 'index-page',
    directives: [UpcomingEvents],
    template: require('./indexPage.html')
})
export class IndexPage {

}