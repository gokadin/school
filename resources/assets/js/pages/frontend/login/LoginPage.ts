import {Component} from 'angular2/core';
import {FORM_DIRECTIVES, ControlGroup, FormBuilder, Validators} from 'angular2/common'
import {Http, Response, Headers, RequestOptions} from 'angular2/http';

import {AuthService} from "./../../../services/authService";

@Component({
    selector: 'login-page',
    template: require('./loginPage.html')
})
export class LoginPage {
    form: ControlGroup;

    constructor(public http: Http, public authService: AuthService, fb: FormBuilder) {
        this.form = fb.group({
            'email': ['', Validators.required],
            'password': ['', Validators.required]
        });
    }

    login(value) {
        this.http.post('/test/api/frontend/account/login', JSON.stringify({
            email: value.email,
            password: value.password
        }))
        .map(res => res.json())
        .subscribe(
            data => {
                this.authService.login(data.authToken);
                window.location.replace('/school/');
            },
            err => console.log(err)
        );
    }
}
