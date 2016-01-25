import {Component} from 'angular2/core';
import moment = require('moment');
import Moment = moment.Moment;

require('./calendar.scss');

import {EventService} from "../../../services/eventService";
import {Event} from "../../../models/event";
import {Modal} from "../../modal/Modal";
import {NewEventModal} from "./newEventModal/NewEventModal";

@Component({
    selector: 'calendar',
    directives: [NewEventModal],
    template: require('./calendar.html')
})
export class Calendar {
    currentDate: any;
    dates: any;
    rows: number[];
    cols: number[];
    newEventModal: Modal;

    constructor(private eventService: EventService) {
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

        this.newEventModal = new NewEventModal();
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

    openNewEventModal(date: Moment): void {
        this.newEventModal.open();
    }
}