import {Injectable, provide} from 'angular2/core';
import {Http, Response} from 'angular2/http';
import {Observable, ReplaySubject} from 'rxjs';

import {User} from '../models/user';

@Injectable()
export class AuthService {
    user: ReplaySubject<User>;

    constructor(public http: Http) {
        this.user = new ReplaySubject<User>();

        this.loadUser();
    }

    loadUser() {
        this.http.get('/api/frontend/account/currentUser')
            .map(data => data.json())
            .subscribe(
                data => {
                    if (data.loggedIn) {
                        this.user.next(new User(data.currentUser));
                    }
                }
            );
    }

    login(email: string, password: string) {
        return this.http.post('/api/frontend/account/login', JSON.stringify({
                email: email,
                password: password
            }))
            .map(res => res.json());
    }

    setToken(authToken: string): void {
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