import {Component} from 'angular2/core';

import {Calendar} from "./../../../../../components/school/calendar/Calendar";

@Component({
    selector: 'index-page',
    directives: [Calendar],
    template: require('./indexPage.html')
})
export class IndexPage {

}