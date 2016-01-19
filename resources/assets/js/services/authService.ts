import {Injectable, provide} from 'angular2/core';
import {Http, Response} from 'angular2/http';
import {Observable, Subject} from 'rxjs';

import {User} from '../models/user';

@Injectable()
export class AuthService {
    user: Subject<User>;

    constructor(public http: Http) {
        this.user = new Subject<User>();

        this.loadUser();
    }

    loadUser() {
        this.http.get('/api/school/currentUser')
            .map(data => { return data.json(); })
            .subscribe(
                data => this.user.next(new User(data))
            );
    }

    login(authToken: string) {
        localStorage.setItem('authToken', authToken);
    }

    logout() {
        localStorage.removeItem('authToken');
    }

    isLoggedIn() {
        return localStorage.getItem('authToken') ? true : false;
    }

    getToken() {
        return localStorage.getItem('authToken');
    }
}

export var AUTH_PROVIDERS: Array<any> = [
    provide(AuthService, {useClass: AuthService})
];