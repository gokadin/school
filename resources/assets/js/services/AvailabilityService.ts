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

    fetchAvailabilities(fromDate: Moment, toDate: Moment): void {
        this.http.get('/api/school/teacher/calendar/availabilities?fromDate=' +
            fromDate.format('YYYY-MM-DD') + '&toDate=' + toDate.format('YYYY-MM-DD'))
            .map(data => data.json())
            .subscribe(
                data => this.availabilities.next(data)
            );
    }

    storeAvailability(availability: Availability): void {
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
}

export var AVAILABILITIES_PROVIDERS = [
    provide(AvailabilityService, {useClass: AvailabilityService})
]