import {Injectable, provide} from 'angular2/core';
import {Http, Response} from 'angular2/http';
import {Observable} from 'rxjs';

@Injectable()
export class ActivityService {

}

export var ACTIVITY_PROVIDERS = [
    provide(ActivityService, {useClass: ActivityService})
];