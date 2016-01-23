import {Component} from 'angular2/core';
import {FORM_DIRECTIVES, FormBuilder, ControlGroup, Validators} from 'angular2/common';
import {Http, Request} from 'angular2/http';
import {Router} from 'angular2/router';

import {Flash} from "../../../flash/Flash";

@Component({
    selector: 'create-activity-form',
    directives: [FORM_DIRECTIVES],
    template: require('./createActivityForm.html')
})
export class CreateActivityForm {
    form:ControlGroup;
    submitEnabled:boolean = true;
    periods:Object[];
    createAnother:boolean;

    constructor(fb:FormBuilder, private http:Http, private router:Router, private flash:Flash) {
        flash.show(); // NOT WORKING!!!!!!!!!!!!!!!!!!!!!!!!!
        this.initializePeriods();

        this.form = fb.group({
            name: ['a', Validators.required],
            rate: [2, Validators.required],
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

    onSubmit(value:Object):void {
        if (!this.form.valid || !this.submitEnabled) {
            this.form.find('name').markAsTouched();
            this.form.find('rate').markAsTouched();

            return;
        }

        this.submitEnabled = false;
        this.submit(value);
        this.afterSubmit();
    }

    submit(value:Object):void {
        this.http.post('/api/school/teacher/activities/', JSON.stringify(value))
            .subscribe(
                () => {
                    this.submitEnabled = true;
                },
                () => {
                    this.submitEnabled = true;
                }
            );
    }

    afterSubmit():void {
        if (!this.createAnother) {
            this.router.navigate(['/School/Teacher/Activity/Index']);

            return;
        }

        // ...
    }
}
