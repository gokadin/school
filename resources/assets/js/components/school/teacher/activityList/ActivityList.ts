import {Component} from 'angular2/core';
import {Http, Response} from 'angular2/http';
import {Router, ROUTER_DIRECTIVES} from 'angular2/router';
import {Control, AbstractControl} from 'angular2/common';

import {ActivityService} from "../../../../services/ActivityService";
import {Activity} from "../../../../models/Activity";

@Component({
    selector: 'activity-list',
    directives: [ROUTER_DIRECTIVES],
    template: require('./activityList.html')
})
export class ActivityList {
    isLoading: boolean;
    initialDataSize: number;
    hasError: boolean;
    data: any;
    page: number;
    max: number;
    total: number;
    searchRules: Object;

    constructor(private activityService: ActivityService) {
        this.isLoading = true;
        this.initialDataSize = 0;
        this.page = 0;
        this.max = 10;
        this.total = 0;
        this.searchRules = {
            name: new Control(),
            rate: new Control(),
            period: new Control()
        }

        activityService.paginated
            .subscribe(
                (data: any) => {
                    this.isLoading = false;
                    this.data = data.activities;
                    this.page = data.pagination.pageNumber;
                    this.total = data.pagination.totalCount;
                    if (this.initialDataSize == 0) {
                        this.initialDataSize = this.data.length;
                    }
                },
                () => {
                    this.hasError = true;
                }
        );

        this.fetchActivities();

        for (let key in this.searchRules) {
            this.searchRules[key].valueChanges
                .debounceTime(400)
                .distinctUntilChanged()
                .subscribe(
                    () => {
                        this.page = 0;
                        this.fetchActivities();
                    }
                );
        }
    }

    fetchActivities(): void {
        this.activityService.paginate(this.page, this.max, this.searchRules);
    }

    nextPage(): void {
        if ((this.page + 1) * this.max >= this.total) {
            return;
        }

        this.page++;
        this.fetchActivities();
    }

    previousPage(): void {
        if (this.page == 0) {
            return;
        }

        this.page--;
        this.fetchActivities();
    }

    delete(activity: Activity): void {

    }

    update(activity: Activity): void {

    }
}