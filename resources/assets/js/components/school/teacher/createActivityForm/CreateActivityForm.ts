import {Component} from 'angular2/core';
import {FORM_DIRECTIVES, FormBuilder, Validators} from 'angular2/common';
import {Http} from 'angular2/http';
import {Router} from 'angular2/router';

import {Flash} from "../../../flash/Flash";
import {FormComponent} from "../../../FormComponent";

@Component({
    selector: 'create-activity-form',
    directives: [FORM_DIRECTIVES],
    template: require('./createActivityForm.html')
})
export class CreateActivityForm extends FormComponent{
    periods: Object[];
    createAnother: boolean;

    constructor(fb: FormBuilder, http: Http, private router: Router, private flash: Flash) {
        super('/api/school/teacher/activities/', http);

        flash.show(); // NOT WORKING!!!!!!!!!!!!!!!!!!!!!!!!!
        this.initializePeriods();

        this.form = fb.group({
            name: ['', Validators.required],
            rate: ['', Validators.required],
            period: ['month'],
            location: ['']
        });
    }

    initializePeriods():void {
        this.periods = [
            {value: 'lesson', display: 'per lesson'},
            {value: '30mins', display: 'per 30 mins'},
            {value: '45mins', display: 'per 45 mins'},
            {value: 'hour', display: 'per hour'},
            {value: '1hour30mins', display: 'per 1 hour 30 mins'},
            {value: 'month', display: 'per month'},
            {value: 'year', display: 'per year'},
        ];
    }

    afterSubmit(): void {
        if (!this.createAnother) {
            this.router.navigate(['/School/Teacher/Activity/Index']);

            return;
        }

        // ...
    }
}
