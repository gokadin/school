import {Injectable, provide} from 'angular2/core';
import {Http, Response} from 'angular2/http';
import {Observable, Subject} from 'rxjs';

import {Activity} from "../models/Activity";

@Injectable()
export class ActivityService {
    paginated: Subject<Object>;

    constructor(private http: Http) {
        this.paginated = new Subject<Object>();
    }

    paginate(page: number, max: number, searchRules: Object = {}, sortingRules = {}): void {
        this.http.get('/api/school/teacher/activities/paginate?page=' + page + '&max=' +
            max + this.buildSearchParams(searchRules) + this.buildSortParams(sortingRules))
            .map((data: Response) => data.json())
            .map((data: any) => {
                return {
                    pagination: data.pagination,
                    activities: data.activities
                        .map((activity: Object) => new Activity(activity))
                }
            })
            .subscribe(
                (data: Object) => {
                    this.paginated.next(data);
                }
            );
    }

    buildSearchParams(searchRules: Object): string {
        let str = '';

        for (let key in searchRules) {
            if (!searchRules[key].value) {
                continue;
            }

            str += '&search[' + key + ']=' + searchRules[key].value;
        }

        return str;
    }

    buildSortParams(sortingRules: Object): string {
        let str = '';

        for (let key in sortingRules) {
            if (!sortingRules[key].value) {
                continue;
            }

            str += '&sort[' + key + ']=' + sortingRules[key].value;
        }

        return str;
    }
}

export var ACTIVITY_PROVIDERS = [
    provide(ActivityService, {useClass: ActivityService})
];