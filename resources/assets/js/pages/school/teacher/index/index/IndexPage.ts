import {Component} from 'angular2/core';
import {FormBuilder, AbstractControl} from 'angular2/common';

import {UpcomingEvents} from "./../../../../../components/school/teacher/upcomingEvents/UpcomingEvents";
import {Timepicker} from "../../../../../components/timepicker/Timepicker";

@Component({
    selector: 'index-page',
    directives: [UpcomingEvents, Timepicker],
    template: require('./indexPage.html')
})
export class IndexPage {
    test: AbstractControl;

    constructor(fb: FormBuilder) {
        this.form = fb.group({
            'test': ['']
        });

        this.test = this.form.controls['test'];
    }

    onSubmit(data: any) {
        console.log(data);
    }
}