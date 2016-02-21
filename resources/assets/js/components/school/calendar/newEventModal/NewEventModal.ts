import {Component} from 'angular2/core';
import {ControlGroup, AbstractControl, Control, FormBuilder, Validators} from 'angular2/common';

import {Event} from "../../../../models/event";
import {Modal} from "../../../modal/Modal";
import {Datepicker} from "../../../datepicker/Datepicker";

@Component({
    selector: 'new-event-modal',
    directives: [Datepicker],
    template: require('./newEventModal.html')
})
export class NewEventModal extends Modal {
    data: Object;
    callback: Function;
    form: ControlGroup;
    title: AbstractControl;
    description: AbstractControl;
    startDate: AbstractControl;

    constructor(fb: FormBuilder) {
        super();

        this.form = fb.group({
            'title': ['', Validators.required],
            'description': [''],
            'startDate': ['']
        });

        this.title = this.form.controls['title'];
        this.description = this.form.controls['description'];
        this.startDate = this.form.controls['startDate'];
    }

    onSubmit(): void {
        this.callback(this.form.value);
    }

    prepare(data: Object, callback: Function) {
        this.data = data;
        this.callback = callback;
    }
}