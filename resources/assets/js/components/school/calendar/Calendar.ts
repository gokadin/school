import {Component} from 'angular2/core';
import moment = require('moment');
import Moment = moment.Moment;

require('./calendar.scss');

import {EventService} from "../../../services/eventService";
import {Event} from "../../../models/event";
import {NewEventModal} from "./newEventModal/NewEventModal";
import {Modal} from "../../modal/Modal";
import {ControlGroup, AbstractControl, Control, FormBuilder, Validators} from 'angular2/common';

import {TimePipe} from "../../../pipes/school/TimePipe";

@Component({
    selector: 'calendar',
    directives: [NewEventModal],
    pipes: [TimePipe],
    template: require('./calendar.html')
})
export class Calendar {
    currentDate: any;
    dates: any;
    rows: number[];
    cols: number[];
    currentRow: number;
    hours: Object[];
    mode: string;
    weekViewLoaded: boolean = false;

    constructor(private eventService: EventService, fb: FormBuilder) {
        this.loadWeekView(); // remove
        this.mode = 'availability'; // change

        this.currentDate = moment();

        eventService.calendarEvents
            .subscribe(
                (events: Event[]) => this.distributeEvents(events)
            );

        this.today();
    }

    previousMonth(): void { this.currentDate.subtract(1, 'months'); this.loadCurrentMonth(); }
    nextMonth(): void { this.currentDate.add(1, 'months'); this.loadCurrentMonth(); }
    today(): void { this.currentDate = moment(); this.loadCurrentMonth(); }

    loadCurrentMonth(): void {
        this.eventService.fetchCalendarEvents(
            moment(this.currentDate).date(1).subtract(1, 'weeks'),
            moment(this.currentDate).date(this.currentDate.daysInMonth()).add(1, 'weeks')
        );

        this.buildDateArray();
    }

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
                events: [],
                availabilities: []
            });
        }

        // temp
        this.dates[0].availabilities.push({
            startTime: 250,
            endTime: 350
        });
        this.dates[1].availabilities.push({
            startTime: 200,
            endTime: 500
        });
        // temp

        for (let i = 0; i < totalCount / 7; i++) {
            this.rows.push(i);
        }

        for (let i = 0; i < 7; i++) {
            this.cols.push(i);
        }
    }

    loadWeekView(): void {
        this.hours = [];

        for (let i = 1; i < 25; i++) {
            let quarters = [];
            for (let j = 1; j < 4; j++) {
                quarters.push({
                    value: i * 100 + j * 25, formattedAmPm: i < 13 ? i + ':' + j * 15 + 'am' : (i - 12) + ':' + j * 15 + 'pm', formatted24: i + ':' + j * 15
                });
            }

            this.hours.push({
                value: i * 100, formattedAmPm: i < 13 ? i + ':00am' : (i - 12) + 'pm', formatted24: i + 'h',
                quarters: quarters
            });
        }

        this.currentRow = 0;

        this.weekViewLoaded = true;
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
            let index = this.getIndexFromDate(event.startDate);
            if (index < 0 || index > this.dates.length - 1) {
                return;
            }

            this.dates[index].events.push(event);
        });
    }

    handleDragStart(e, event: Event, index: number): void {
        e.dataTransfer.setData('lastIndex', index.toString());
        e.dataTransfer.setData('event', JSON.stringify(event));

        e.dataTransfer.effectAllowed = 'move';
    }

    handleDragOver(e): void {
        e.preventDefault();
    }

    handleDrop(e, index: number): void {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';

        let lastIndex = e.dataTransfer.getData('lastIndex');
        if (lastIndex == index) {
            return;
        }

        let event: Event = new Event(JSON.parse(e.dataTransfer.getData('event')));

        for (let i = 0; i < this.dates[lastIndex].events.length; i++) {
            if (this.dates[lastIndex].events[i].id == event.id &&
                this.dates[lastIndex].events[i].startDate.isSame(event.startDate, 'day')) {
                this.dates[lastIndex].events.splice(i, 1);
                this.dates[index].events.push(event);
                this.eventService.updateDate(event.id, this.dates[lastIndex].date, this.dates[index].date)
                    .subscribe(
                        ((event: Event) => {
                            this.dates[index].events.pop();
                            this.dates[index].events.push(event);
                        })
                    );

                break;
            }
        }
    }

    openNewEventModal(date: Moment, modal: NewEventModal): void {
        modal.prepare({
            startDate: date,
            endDate: date,
            startTime: '12:00',
            endTime: '13:00',
            isAllDay: true
        }, this.createNewEvent);

        modal.open();
    }

    createNewEvent(data: Object): void {
        console.log(data);
    }

    showMonthlyCalendar(): void {
        if (this.mode == 'month') {
            return;
        }

        this.mode = 'month';
    }

    showWeeklyCalendar(): void {
        if (this.mode == 'week') {
            return;
        }

        if (!this.weekViewLoaded) {
            this.loadWeekView();
        }

        this.mode = 'week';
    }

    enterAvailabilityMode(): void {
        if (this.mode != 'week' && !this.weekViewLoaded) {
            this.loadWeekView();
        }

        this.mode = 'availability';
    }

    exitAvailabilityMode(): void {
        if (this.mode != 'availability') {
            return;
        }

        this.mode = 'week';
    }

    isTimeAvailable(col: number, start: number, end: number): boolean {
        let availabilities = this.dates[this.currentRow * 7 + col].availabilities;

        for (let i = 0; i < availabilities.length; i++) {
            if ((availabilities[i].startTime > start && availabilities[i].startTime < end) ||
                (availabilities[i].endTime > start && availabilities[i].endTime < end)) {
                return true;
            }
        }

        return false;
    }

    createAvailability(col: number, start: number, end: number): void {
        if (this.isTimeAvailable(col, start, end)) {
            return;
        }

        this.dates[this.currentRow * 7 + col].availabilities.push({
            startTime: start,
            endTime: end
        });
    }
}