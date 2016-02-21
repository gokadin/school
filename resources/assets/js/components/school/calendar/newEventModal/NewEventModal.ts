import {Component} from 'angular2/core';
import {ControlGroup, AbstractControl, Control, FormBuilder, Validators} from 'angular2/common';

import {Event} from "../../../../models/event";
import {Modal} from "../../../modal/Modal";
import {Datepicker} from "../../../datepicker/Datepicker";
import moment = require("moment");

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

    constructor(private fb: FormBuilder) {
        super();

        this.data = {
            startDate: moment()
        };

        this.buildForm();
    }

    onSubmit(): void {
        this.callback(this.form.value);
    }

    buildForm(): void {
        this.form = this.fb.group({
            'title': ['', Validators.required],
            'description': [''],
            'startDate': [this.data.startDate.format('YYYY-MM-DD')]
        });

        this.title = this.form.controls['title'];
        this.description = this.form.controls['description'];
        this.startDate = this.form.controls['startDate'];
    }

    prepare(data: Object, callback: Function) {
        this.data = data;
        this.callback = callback;

        this.buildForm();
    }
}