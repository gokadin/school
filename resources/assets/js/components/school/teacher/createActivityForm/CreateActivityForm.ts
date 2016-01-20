import {Component} from 'angular2/core';
import {FORM_DIRECTIVES, FormBuilder, ControlGroup, Validators} from 'angular2/common';

@Component({
    selector: 'create-activity-form',
    directives: [FORM_DIRECTIVES],
    template: require('./createActivityForm.html')
})
export class CreateActivityForm {
    form: ControlGroup;

    constructor(fb: FormBuilder) {
        this.form = fb.group({
            name: ['', Validators.required],
            rate: ['', Validators.required]
        });
    }

    onSubmit(value: Object): void {
        console.log(value);
    }
}