import {Component} from 'angular2/core';
import {Http, Response} from 'angular2/http';

@Component({
    selector: 'activity-list',
    template: require('./activityList.html')
})
export class ActivityList {
    activities: Object[];
    nameSearch: string;
    rateSearch: string;
    periodSearch: string;

    constructor(private http: Http) {
        this.fetchActivities();
    }

    fetchActivities(): void {
        this.http.get('/api/school/teacher/activities/paginate?page=0&max=10')
            .map(data => data.json())
            .subscribe(
                data => {
                    this.activities = data.activities;
                }
            );
    }
}