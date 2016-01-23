import {Component} from 'angular2/core';
import moment = require('moment');

require('./calendar.scss');

import {EventService} from "../../../services/eventService";
import {Event} from "../../../models/event";
import Moment = moment.Moment;

@Component({
    selector: 'calendar',
    template: require('./calendar.html')
})
export class Calendar {
    currentDate: any;
    dates: any;
    rows: number[];
    cols: number[];

    constructor(eventService: EventService) {
        this.currentDate = moment();

        eventService.calendarEvents
            .subscribe(
                (events: Event[]) => this.distributeEvents(events)
            );

        eventService.fetchCalendarEvents(
            moment(this.currentDate).date(1).subtract(1, 'weeks'),
            moment(this.currentDate).date(this.currentDate.daysInMonth()).add(1, 'weeks')
        );

        this.today();
    }

    previousMonth(): void { this.currentDate.substract(1, 'months'); this.buildDateArray(); }
    nextMonth(): void { this.currentDate.add(1, 'months'); this.buildDateArray(); }
    today(): void { this.currentDate = moment(); this.buildDateArray(); }

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
            this.dates.push({
                date: moment(startDate.add(1, 'days')),
                events: []
            });
        }

        for (let i = 0; i < totalCount / 7; i++) {
            this.rows.push(i);
        }

        for (let i = 0; i < 7; i++) {
            this.cols.push(i);
        }
    }

    getIndexFromDate(date: Moment): number {
        let offset = this.currentDate.date(1).weekday();

        if (date.month() == this.currentDate.month()) {
            return date.date() + offset;
        }

        let lastMonth = moment(this.currentDate).subtract(1, 'months');
        if (date.month() == lastMonth.month()) {
            return offset - (lastMonth.daysInMonth() - date.date());
        }

        let nextMonth = moment(this.currentDate).add(1, 'months');
        if (date.month() == nextMonth.month()) {
            return this.currentDate.daysInMonth() + offset + date.date() - 1;
        }

        return -1;
    }

    distributeEvents(events: Event[]): void {
        events.forEach((event: Event) => {
            this.dates[this.getIndexFromDate(event.startDate)].events.push(event);
        });
    }
}