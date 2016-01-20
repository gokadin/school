import {Component} from 'angular2/core';

require('./upcomingEvents.scss');

import {ApiComponent} from "../../../ApiComponent";
import {EventService} from "../../../../services/eventService";

@Component({
    selector: 'upcoming-events',
    template: require('./upcomingEvents.html')
})
export class UpcomingEvents extends ApiComponent {
    constructor(eventService: EventService) {
        super();

        this.subscribeToSource(eventService.groupedUpcomingEvents);
    }
}