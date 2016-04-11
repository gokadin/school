import {SpyObject} from 'angular2/testing_internal';

import {EventService} from "../../../resources/assets/js/services/eventService";

export class MockEventService extends SpyObject {
    fakeResponse;
    calendarEvents;
    fetchCalendarEvents;
    updateDate;

    constructor() {
        super(EventService);

        this.fakeResponse = [];
        this.fetchCalendarEvents = this.spy('fetchCalendarEvents').andReturn(this);
        this.updateDate = this.spy('updateDate').andReturn(this);

        this.calendarEvents = {
            subscribe: (callback) => callback(this.fakeResponse)
        };
    }

    setResponse(response: any): void {
        this.fakeResponse = response;
    }
}