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
    controlValue: string;
    show: boolean;
    mode: string;
    hours: Array<string>;
    minutes: Array<string>;
    selectedHour: string;
    selectedMinute: string;

    constructor() {
        this.controlValue = '';

        this.hours = ['00'];
        for (let i = 1; i < 24; i++) {
            this.hours.push(i.toString());
        }

        this.minutes = ['00', '05'];
        for (let i = 10; i < 60; i+= 5) {
            this.minutes.push(i.toString());
        }

        this.selectDefaultTime();

        this.mode = 'hours';
    }

    selectDefaultTime(): void {
        this.selectedHour = '12';
        this.selectedMinute = '00';
    }

    isTimeValid(timeString: string): boolean {
        return /^[0-9]{1,2}:[0-9]{2}$/.test(timeString);
    }

    open() {
        if (this.show) {
            return;
        }

        this.mode = 'hours';

        if (this.control && this.isTimeValid(this.control.value)) {
            let hourAndMinute = this.control.value.split(':');
            this.selectedHour = hourAndMinute[0];
            this.selectedMinute = hourAndMinute[1];
        } else {
            this.selectDefaultTime();
        }

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

        if (this.controlValue == '' || !this.isTimeValid(this.controlValue)) {
            this.controlValue = hour + ':' + this.selectedMinute;
        } else {
            this.controlValue = hour + ':' + this.controlValue.split(':')[1];
        }
    }

    selectMinute(minute: string, e): void {
        e.stopPropagation();

        this.selectedMinute = minute;

        if (this.controlValue == '' || !this.isTimeValid(this.controlValue)) {
            this.controlValue = this.selectedHour + ':' + minute;
        } else {
            this.controlValue = this.controlValue.split(':')[0] + ':' + minute;
        }

        this.close();
    }
}