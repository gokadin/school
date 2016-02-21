import {Component, Input, OnInit} from 'angular2/core';
import {AbstractControl} from 'angular2/common';
import moment = require('moment');
import Moment = moment.Moment;

require('./datepicker.scss');

@Component({
    selector: 'datepicker',
    template: require('./datepicker.html')
})
export class Datepicker {
    @Input() control: AbstractControl;
    @Input() placeholder: string;
    show: boolean;
    currentDate: any;
    rows: number[];
    cols: number[];
    dates: any;
    controlValue: string;

    constructor() {
        this.controlValue = '';

        this.currentDate = moment();
        this.buildDateArray();
    }

    open() {
        if (this.show) {
            return;
        }

        this.today();

        this.show = true;
    }

    close() {
        if (!this.show) {
            return;
        }

        this.show = false;
    }

    selectDate(date: Moment, e): void {
        e.stopPropagation();

        this.controlValue = date.format('YYYY-MM-DD');

        this.close();
    }

    today(): void { this.currentDate = moment(); this.buildDateArray(); }
    previousMonth(): void { this.currentDate.subtract(1, 'months'); this.buildDateArray(); }
    nextMonth(): void { this.currentDate.add(1, 'months'); this.buildDateArray(); }
    previousYear(): void { this.currentDate.subtract(1, 'years'); this.buildDateArray(); }
    nextYear(): void { this.currentDate.add(1, 'years'); this.buildDateArray(); }

    buildDateArray(): void {
        this.dates = [];
        this.rows = [];
        this.cols = [];

        let weekDay = this.currentDate.date(1).weekday();
        let startDate = moment(this.currentDate).subtract(weekDay + 1, 'days');
        let totalCount = weekDay + this.currentDate.daysInMonth();
        if (totalCount % 7 != 0) {
            totalCount = totalCount + 7 - totalCount % 7;
        }

        for (let i = 0; i < totalCount; i++) {
            this.dates.push(moment(startDate.add(1, 'days')));
        }

        for (let i = 0; i < totalCount / 7; i++) {
            this.rows.push(i);
        }

        for (let i = 0; i < 7; i++) {
            this.cols.push(i);
        }
    }
}