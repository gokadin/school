import {Injectable, provide} from 'angular2/core';
import {Http} from 'angular2/http';
import {Observable, Subject} from 'rxjs';

@Injectable()
export class AccountService {
    profile: Subject<Object>;

    constructor(private http: Http) {
        this.profile = new Subject<Object>();
    }
}

export var ACCOUNT_PROVIDERS = [
    provide(AccountService, {useClass: AccountService})
];

