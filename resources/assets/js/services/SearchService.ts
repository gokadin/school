import {Injectable, provide} from 'angular2/core';
import {Http} from 'angular2/http';
import {Observable, Subject} from 'rxjs';
import {Student} from "../models/Student";
import {Activity} from "../models/Activity";

@Injectable()
export class SearchService {
    generalResults:Subject<Object>;

    constructor(private http:Http) {
        this.generalResults = new Subject<Object>();
    }

    fetchGeneralResults(search: string): void {
        this.http.get('/api/school/teacher/search/general/' + encodeURI(search))
            .map(data => data.json())
            .map(data => {
                return {
                    students: data.students.map(student => new Student(student)),
                    activities: data.activities.map(activity => new Activity(activity))
                };
            })
            .subscribe(
                data => this.generalResults.next(data)
            );
    }
}

export var SEARCH_PROVIDERS = [
    provide(SearchService, {useClass: SearchService})
]