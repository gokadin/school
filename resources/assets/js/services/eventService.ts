import {Injectable, provide} from 'angular2/core';
import {Http, Response} from 'angular2/http';
import {Observable, Subject} from 'rxjs';
import moment = require('moment');

import {Event} from "../models/event";
import Moment = moment.Moment;

@Injectable()
export class EventService {
    upcomingEvents: Observable<Event[]>;
    groupedUpcomingEvents: Observable<Object[]>;
    calendarEvents: Subject<Event[]>;

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
            });

        this.calendarEvents = new Subject<Event[]>();
    }

    fetchCalendarEvents(from: Moment, to: Moment): void {
        this.http.get('/api/school/teacher/events/range?from=' +
            from.format('YYYY-MM-DD') + '&to=' + to.format('YYYY-MM-DD'))
            .map((data: Response) => data.json())
            .map((events: Object[]) => events
                .map((event: Object) => new Event(event)))
            .subscribe(
                (events: Event[]) => this.calendarEvents.next(events)
            );
    }

    updateDate(id: number, oldDate: Moment, date: Moment): Observable<Event> {
        return this.http.patch('/api/school/teacher/events/' + id + '/' +
            oldDate.format('YYYY-MM-DD') + '/date', JSON.stringify({
            date: date.format('YYYY-MM-DD')
        }))
            .map((data: Response) => data.json())
            .map((event: Object) => new Event(event));
    }
}

export var EVENT_PROVIDERS = [
    provide(EventService, {useClass: EventService})
];