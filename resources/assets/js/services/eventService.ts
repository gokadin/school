import {Injectable, provide} from 'angular2/core';
import {Http} from 'angular2/http';
import {Observable} from 'rxjs';
import moment = require('moment');

import {Event} from "../models/event";

@Injectable()
export class EventService {
    groupedUpcomingEvents: Observable<any>;

    constructor(private http: Http) {
        this.groupedUpcomingEvents = this.http.get('/api/school/teacher/events/upcoming')
            .map(data => data.json());
    }
}

export var EVENT_PROVIDERS = [
    provide(EventService, {useClass: EventService})
];