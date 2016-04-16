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

    constructor(public authService: AuthService, fb: FormBuilder) {
        this.form = fb.group({
            'email': ['', Validators.required],
            'password': ['', Validators.required]
        });
    }

    login(value) {
        this.authService.login(value.email, value.password)
            .subscribe(
                data => {
                    this.authService.setToken(data.authToken);
                    location.href = '/school/';
                },
                err => console.log(err)
            );
    }
}
