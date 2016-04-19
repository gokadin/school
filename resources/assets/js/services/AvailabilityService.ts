import {Injectable, provide} from 'angular2/core';
import {Http, Response} from 'angular2/http';
import {Subject} from 'rxjs';

import {Availability} from "../models/Availability";
import Moment = moment.Moment;

@Injectable()
export class AvailabilityService {
    availabilities: Subject<Object>;

    constructor(private http: Http) {
        this.availabilities = new Subject<Object>();
    }

    fetch(date: Moment): void {
        this.http.get('/api/school/teacher/calendar/availabilities/?weekStartDate=' + date.format('YYYY-MM-DD'))
            .map(data => data.json())
            .map(data => data.availabilities
                .map(availability => new Availability(availability)))
            .subscribe(
                data => this.availabilities.next(data)
            );
    }

    store(availability: Availability): void {
        this.http.post('/api/school/teacher/calendar/availabilities/', JSON.stringify({
            date: availability.date.format('YYYY-MM-DD'),
            startTime: availability.startTime,
            endTime: availability.endTime
        }))
            .map(data => data.json())
            .subscribe(
                data => {
                    availability.id = data.id;
                    this.availabilities.next(availability);
                }
            );
    }

    update(availability: Availability) {
        return this.http.put('/api/school/teacher/calendar/availabilities/' + availability.id, JSON.stringify({
            date: availability.date.format('YYYY-MM-DD'),
            startTime: availability.startTime,
            endTime: availability.endTime
        }));
    }

    delete(availability: Availability) {
        return this.http.delete('/api/school/teacher/calendar/availabilities/' + availability.id + 'a' + availability.date.format('YYYY-MM-DD'));
    }

    applyToFutureWeeks(date: Moment) {
        return this.http.post('/api/school/teacher/calendar/availabilities/apply-to-future-weeks',
            JSON.stringify({date: date.format('YYYY-MM-DD')}));
    }
}

export var AVAILABILITIES_PROVIDERS = [
    provide(AvailabilityService, {useClass: AvailabilityService})
]