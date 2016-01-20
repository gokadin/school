import {Injectable, provide} from 'angular2/core';
import {Http, Response} from 'angular2/http';
import {Observable} from 'rxjs';
import moment = require('moment');

import {Event} from "../models/event";

@Injectable()
export class EventService {
    upcomingEvents: Observable<Event[]>;
    groupedUpcomingEvents: Observable<Object[]>;

    constructor(private http: Http) {
        this.upcomingEvents = this.http.get('/api/school/teacher/events/upcoming')
            .map((data: Response) => data.json())
            .map((events: Object[]) => events
                .map((event: Object) => new Event(event)));

        this.groupedUpcomingEvents = this.upcomingEvents
            .map((events: Event[]) => {
                return events.reduce((grouped, event) => {
                    if (grouped.length == 3) {
                        return grouped;
                    }
                    let found = false;
                    grouped.forEach(group => {
                        if (group.date.isSame(event.startDate, 'day')) {
                            group.events.push(new Event(event));
                            found = true;
                        }
                    });
                    if (!found) {
                        grouped.push({date: event.startDate, events: [new Event(event)]});
                    }
                    return grouped;
                }, [])
            })
            ;
    }
}

export var EVENT_PROVIDERS = [
    provide(EventService, {useClass: EventService})
];