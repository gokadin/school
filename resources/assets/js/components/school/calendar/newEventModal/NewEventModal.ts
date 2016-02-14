import {Component} from 'angular2/core';
import {ControlGroup, AbstractControl, Control, FormBuilder, Validators} from 'angular2/common';

import {Event} from "../../../../models/event";
import {Modal} from "../../../modal/Modal";

@Component({
    selector: 'new-event-modal',
    template: require('./newEventModal.html')
})
export class NewEventModal extends Modal {
    form: ControlGroup;
    title: AbstractControl;
    description: AbstractControl;

    constructor(fb: FormBuilder) {
        super();

        this.form = fb.group({
            'title': ['', Validators.required],
            'description': ['']
        });

        this.title = this.form.controls['title'];
        this.description = this.form.controls['description'];
    }

    onSubmit(value: any): void {
        console.log('submitted: ', value);
    }
}