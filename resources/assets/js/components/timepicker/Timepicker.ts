import {Component, Input} from 'angular2/core';
import {AbstractControl} from 'angular2/common';

require('./timepicker.scss');

@Component({
    selector: 'timepicker',
    template: require('./timepicker.html')
})
export class Timepicker {
    @Input() control: AbstractControl;
    @Input() placeholder: string;
    show: boolean = true;
    mode: string;
    hours: Array<string>;
    minutes: Array<string>;
    selectedHour: string;
    selectedMinute: string;

    constructor() {
        this.hours = ['00'];
        for (let i = 1; i < 24; i++) {
            this.hours.push(i.toString());
        }

        this.minutes = ['00'];
        for (let i = 1; i < 60; i++) {
            this.minutes.push(i.toString());
        }

        this.selectedHour = '12';
        this.selectedMinute = '00';

        this.mode = 'minutes';
    }

    open() {
        if (this.show) {
            return;
        }

        this.mode = 'hours';

        // ... parse

        this.show = true;
    }

    close() {
        if (!this.show) {
            return;
        }

        this.show = false;
    }

    changeMode(mode: string): void {
        switch (mode) {
            case 'minutes':
                if (this.mode != 'minutes') {
                    this.mode = 'minutes';
                }
                break;
            default:
                if (this.mode != 'hours') {
                    this.mode = 'hours';
                }
                break;
        }
    }

    selectHour(hour: string): void {
        this.selectedHour = hour;
        this.changeMode('minutes');
    }

    selectMinute(minute: string): void {
        this.selectedMinute = minute;
        //this.close();
    }
}