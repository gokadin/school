import {Component} from 'angular2/core';
import {FORM_DIRECTIVES, ControlGroup, FormBuilder, Validators} from 'angular2/common'
import {Http, Response, Headers, RequestOptions} from 'angular2/http';

import {AuthService} from "../../../../services/authService";

@Component({
    selector: 'login',
    template: require('./login.html')
})
export class Login {
    form: ControlGroup;

    constructor(public http: Http, public authService: AuthService, fb: FormBuilder) {
        this.form = fb.group({
            'email': ['', Validators.required],
            'password': ['', Validators.required]
        });
    }

    login(value) {
        let headers: Headers = new Headers();
        headers.set('Content-Type', 'text/json; charset=UTF-8');
        let opts: RequestOptions = new RequestOptions();
        opts.headers = headers;

        this.http.post('/test/api/frontend/account/login', JSON.stringify({
            email: value.email,
            password: value.password
        }), opts)
        .map(res => res.json())
        .subscribe(
            data => this.authService.login(data.authToken),
            err => console.log(err)
        );
    }
}
