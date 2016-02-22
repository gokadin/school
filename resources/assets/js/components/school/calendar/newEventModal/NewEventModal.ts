import {Component} from 'angular2/core';
import {ControlGroup, AbstractControl, Control, FormBuilder, Validators} from 'angular2/common';
import moment = require("moment");

import {Event} from "../../../../models/event";
import {Modal} from "../../../modal/Modal";
import {Datepicker} from "../../../datepicker/Datepicker";
import {Timepicker} from "../../../timepicker/Timepicker";

require('./newEventModal.scss');

@Component({
    selector: 'new-event-modal',
    directives: [Datepicker, Timepicker],
    template: require('./newEventModal.html')
})
export class NewEventModal extends Modal {
    data: Object;
    callback: Function;
    eventColors: Array<string>;
    selectedColor: string;
    showMoreOptions: boolean;
    form: ControlGroup;
    title: AbstractControl;
    description: AbstractControl;
    startDate: AbstractControl;
    startTime: AbstractControl;
    endDate: AbstractControl;
    endTime: AbstractControl;
    isAllDay: AbstractControl;
    isRecurring: AbstractControl;
    rRepeat: AbstractControl;
    rEndsNever: AbstractControl;
    rEndDate: AbstractControl;

    constructor(private fb: FormBuilder) {
        super();

        this.data = {
            startDate: moment(),
            startTime: '12:00',
            endDate: moment(),
            endTime: '13:00'
        };

        this.eventColors = ['teal', 'green', 'orange', 'blue', 'light-blue', 'golden', 'red', 'purple'];
        this.selectedColor = this.eventColors[0];

        this.buildForm();
    }

    buildForm(): void {
        this.form = this.fb.group({
            'title': ['', Validators.required],
            'description': [''],
            'startDate': [this.data.startDate.format('YYYY-MM-DD')],
            'startTime': [this.data.startTime],
            'endDate': [this.data.endDate.format('YYYY-MM-DD')],
            'endTime': [this.data.endTime],
            'isAllDay': [this.data.isAllDay],
            'isRecurring': [false],
            'rRepeat': ['weekly'],
            'rEndsNever': [true],
            'rEndDate': [this.data.endDate.format('YYYY-MM-DD')]
        });

        this.title = this.form.controls['title'];
        this.description = this.form.controls['description'];
        this.startDate = this.form.controls['startDate'];
        this.startTime = this.form.controls['startTime'];
        this.endDate = this.form.controls['endDate'];
        this.endTime = this.form.controls['endTime'];
        this.isAllDay = this.form.controls['isAllDay'];
        this.isRecurring = this.form.controls['isRecurring'];
        this.rRepeat = this.form.controls['rRepeat'];
        this.rEndsNever = this.form.controls['rEndsNever'];
        this.rEndDate = this.form.controls['rEndDate'];
    }

    prepare(data: Object, callback: Function): void {
        this.data = data;
        this.callback = callback;

        this.buildForm();
        this.showMoreOptions = false;
    }

    onSubmit(): void {
        let formValues = this.form.value;
        formValues['color'] = this.selectedColor;

        this.callback(formValues);
    }

    selectColor(color: string): void {
        if (this.eventColors.indexOf(color) == -1) {
            this.selectedColor = this.eventColors[0];

            return;
        }

        this.selectedColor = color;
    }
}