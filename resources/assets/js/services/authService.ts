import {Injectable, provide} from 'angular2/core';

@Injectable()
export class AuthService {
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