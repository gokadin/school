import {Component, Input} from 'angular2/core';
import {ROUTER_DIRECTIVES, Router} from 'angular2/router';

require('./switcher.scss');

@Component({
    selector: 'switcher',
    directives: [ROUTER_DIRECTIVES],
    template: require('./switcher.html')
})
export class Switcher {
    @Input() links: Array<Object>;

    constructor(private router: Router) {

    }
}