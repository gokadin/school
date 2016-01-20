import {Component} from 'angular2/core';
import {FORM_DIRECTIVES, FormBuilder, ControlGroup, Validators} from 'angular2/common';

@Component({
    selector: 'create-activity-form',
    directives: [FORM_DIRECTIVES],
    template: require('./createActivityForm.html')
})
export class CreateActivityForm {
    form: ControlGroup;
    submitEnabled: boolean = true;
    periods: Object[];

    constructor(fb: FormBuilder) {
        this.initializePeriods();

        this.form = fb.group({
            name: ['', Validators.required],
            rate: ['', Validators.required],
            period: ['month'],
            location: ['']
        });
    }

    initializePeriods (): void {
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

    onSubmit(value: Object): void {
        if (!this.form.valid || !this.submitEnabled) {
            console.log('form invalid');
            this.form.find('name').markAsTouched();
            this.form.find('rate').markAsTouched();
            return;
        }

        this.submitEnabled = false;
        console.log(value);
    }
}