import {RequestOptions, Headers} from 'angular2/http';

export class AppRequestOptions extends RequestOptions {
    constructor() {
        super({
            headers: new Headers({
                'Content-Type': 'text/json; charset=UTF-8',
                'CSRFTOKEN': document.getElementById('csrf-token').getAttribute('content'),
                'Authorization': 'Bearer ' + localStorage.getItem('authToken')
            })
        });
    }
}