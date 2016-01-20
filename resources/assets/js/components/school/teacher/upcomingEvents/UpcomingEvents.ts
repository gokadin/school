import {Component} from 'angular2/core';

import {EventService} from "../../../../services/eventService";

@Component({
    selector: 'upcoming-events',
    template: require('./upcomingEvents.html')
})
export class UpcomingEvents {
    groupedUpcomingEvents: Array<any>;
    upcomingEventsLoaded: boolean;

    constructor(eventService: EventService) {
        this.upcomingEventsLoaded = false;

        eventService.groupedUpcomingEvents.subscribe(
            events => {
                this.groupedUpcomingEvents = events;
                this.upcomingEventsLoaded = true;
            }
        );
    }
}