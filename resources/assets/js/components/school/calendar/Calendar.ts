import {Component} from 'angular2/core';
import moment = require('moment');
import Moment = moment.Moment;

require('./calendar.scss');

import {EventService} from "../../../services/eventService";
import {Event} from "../../../models/event";
import {NewEventModal} from "./newEventModal/NewEventModal";
import {Modal} from "../../modal/Modal";

import {TimePipe} from "../../../pipes/school/TimePipe";
import {AvailabilityService} from "../../../services/AvailabilityService";
import {Availability} from "../../../models/Availability";

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
    resizing: boolean = false;
    resizeStartPosition: number;
    currentAvailability: Availability;
    currentAvailabilitySiblings: Availability[];

    constructor(private eventService: EventService,
                private availabilityService: AvailabilityService) {
        document.addEventListener('mouseup', e => this.handleAvailabilityResizeMouseUp());

        this.currentDate = moment();

        this.buildDateArray();
        this.loadWeekView(); // remove
        this.mode = 'availability'; // change

        eventService.calendarEvents
            .subscribe(
                (events: Event[]) => this.distributeEvents(events)
            );

        availabilityService.availabilities
            .subscribe(
                (availabilities: Availability[]) => this.distributeAvailabilities(availabilities)
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

        for (let i = 0; i < totalCount / 7; i++) {
            this.rows.push(i);
        }

        for (let i = 0; i < 7; i++) {
            this.cols.push(i);
        }
    }

    loadWeekView(): void {
        this.currentRow = 0;

        this.availabilityService.fetch(this.dates[this.currentRow * 7].date, this.dates[this.currentRow * 7 + 6].date);

        this.hours = [];

        for (let i = 1; i < 25; i++) {
            let quarters = [];
            for (let j = 0; j < 4; j++) {
                quarters.push({
                    value: i * 100 + j * 25, formattedAmPm: i < 13 ? i + ':' + j * 15 + 'am' : (i - 12) + ':' + j * 15 + 'pm', formatted24: i + ':' + j * 15
                });
            }

            this.hours.push({
                value: i * 100, formattedAmPm: i < 13 ? i + ':00am' : (i - 12) + 'pm', formatted24: i + 'h',
                quarters: quarters
            });
        }

        this.weekViewLoaded = true;
    }

    getIndexFromDate(date: Moment): number {
        let offset = this.currentDate.date(1).weekday() - 1;

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

    distributeAvailabilities(availabilities: Availability[]): void {
        if (!Array.isArray(availabilities)) {
            this.distributeAvailability(availabilities);
            return;
        }

        availabilities.forEach(availability => {
            this.distributeAvailability(availability);
        });
    }

    distributeAvailability(availability: Availability): void {
        let index = this.getIndexFromDate(availability.date);

        if (index < 0 || index > this.dates.length - 1) {
            return;
        }

        this.dates[index].availabilities.push(availability);
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
        if (!this.weekViewLoaded) {
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

        this.availabilityService.store(new Availability({
            date: this.dates[this.currentRow * 7 + col].date,
            startTime: start,
            endTime: end
        }));
    }

    handleAvailabilityResizeMouseDown(event, availability: Availability, col): void {
        event.preventDefault();
        this.resizing = true;
        this.currentAvailability = availability;
        this.resizeStartPosition = event.clientY;

        this.currentAvailabilitySiblings = [];
        let availabilities = this.dates[this.currentRow * 7 + col].availabilities;
        for (let i = 0; i < availabilities.length; i++) {
            if (availabilities[i].id != availability.id &&
                availabilities[i].position >= availability.position + availability.height) {
                this.currentAvailabilitySiblings.push(availabilities[i]);
            }
        }
    }

    handleAvailabilityResizeMouseUp(): void {
        if (!this.resizing) {
            return;
        }

        if (this.currentAvailability.originalHeight == this.currentAvailability.height) {
            return;
        }

        this.currentAvailability.endTime += (this.currentAvailability.height - this.currentAvailability.originalHeight) / 12 * 25;
        this.currentAvailability.originalHeight = this.currentAvailability.height;

        this.availabilityService.update(this.currentAvailability)
            .subscribe();

        this.currentAvailability = null;
        this.resizing = false;
    }

    handleAvailabilityResizeMouseMove(event): void {
        if (!this.resizing) {
            return;
        }

        let height = this.currentAvailability.originalHeight + event.clientY - this.resizeStartPosition;

        if (height < 12) {
            this.currentAvailability.height = 12;
            return;
        }

        if (height % 12 != 0) {
            return;
        }

        for (let i = 0; i < this.currentAvailabilitySiblings.length; i++) {
            if (this.currentAvailability.position + height >= this.currentAvailabilitySiblings[i].position) {
                this.currentAvailability.height = this.currentAvailabilitySiblings[i].position - this.currentAvailability.position;
                return;
            }
        }

        this.currentAvailability.height = height;
    }

    handleAvailabilityDragStart(event, availability: Availability, col: number): void {
        if (this.resizing) {
            this.handleAvailabilityResizeMouseUp();
        }

        event.dataTransfer.setData('id', availability.id.toString());
        event.dataTransfer.setData('oldCol', col.toString());
        event.dataTransfer.effectAllowed = 'move';
    }

    handleAvailabilityDragOver(event): void {
        event.preventDefault();
    }

    handleAvailabilityDrop(event, col: number, newStartTime: number): void {
        event.preventDefault();
        event.dataTransfer.dropEffect = 'move';

        let id = parseInt(event.dataTransfer.getData('id'));
        let oldCol = parseInt(event.dataTransfer.getData('oldCol'));

        let oldAvailabilities = this.dates[this.currentRow * 7 + oldCol].availabilities;
        let availability = null;
        let availabilityIndex = 0;
        for (let i = 0; i < oldAvailabilities.length; i++) {
            if (oldAvailabilities[i].id == id) {
                availability = oldAvailabilities[i];
                availabilityIndex = i;
                break;
            }
        }

        if (availability == null) {
            return;
        }

        availability.endTime += newStartTime - availability.startTime;
        availability.startTime = newStartTime;
        availability.calculatePosition();

        if (oldCol != col) {
            availability.date = moment(this.dates[this.currentRow * 7 + col].date);
            this.dates[this.currentRow * 7 + oldCol].availabilities.splice(availabilityIndex, 1);
            this.dates[this.currentRow * 7 + col].availabilities.push(availability);
        }

        availability = this.mergeAvailabilitiesOnDrop(availability, col);

        if (availability.endTime > 2500) {
            availability.endTime = 2500;
        }

        availability.calculateHeight();

        this.availabilityService.update(availability)
            .subscribe();
    }

    mergeAvailabilitiesOnDrop(availability: Availability, col: number): void {
        let availabilities = this.dates[this.currentRow * 7 + col].availabilities;
        let toMergeIndexes = [];
        for (let i = 0; i < availabilities.length; i++) {
            if (availabilities[i].id == availability.id) {
                continue;
            }

            if (availabilities[i].startTime < availability.endTime && availabilities[i].endTime > availability.startTime) {
                toMergeIndexes.push(i);
            }
        }

        let highest = availability.endTime;
        for (let i = toMergeIndexes.length - 1; i >= 0; i--) {
            if (availabilities[toMergeIndexes[i]].endTime > highest) {
                highest = availabilities[toMergeIndexes[i]].endTime;
            }

            this.availabilityService.delete(availabilities[toMergeIndexes[i]])
                .subscribe();
            availabilities.splice(toMergeIndexes[i], 1);
        }

        availability.endTime = highest;

        return availability;
    }
}