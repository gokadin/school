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

    fetch(fromDate: Moment, toDate: Moment): void {
        this.http.get('/api/school/teacher/calendar/availabilities/range?fromDate=' +
            fromDate.format('YYYY-MM-DD') + '&toDate=' + toDate.format('YYYY-MM-DD'))
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

    update(availability: Availability): void {
        return this.http.put('/api/school/teacher/calendar/availabilities/' + availability.id, JSON.stringify({
            date: availability.date.format('YYYY-MM-DD'),
            startTime: availability.startTime,
            endTime: availability.endTime
        }));
    }
}

export var AVAILABILITIES_PROVIDERS = [
    provide(AvailabilityService, {useClass: AvailabilityService})
]