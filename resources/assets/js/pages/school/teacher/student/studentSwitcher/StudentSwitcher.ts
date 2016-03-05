import {Component} from 'angular2/core';
import {RouteParams} from 'angular2/router';

import {Switcher} from "../../../../../components/switcher/Switcher";

@Component({
    selector: 'student-switcher',
    directives: [Switcher],
    template: require('./studentSwitcher.html')
})
export class StudentSwitcher {
    switcherLinks: Array<Object>;

    constructor(params: RouteParams) {
        let id = params.get('id');

        this.switcherLinks = [
            {route: ['/School/Teacher/Student/Show', {id: id}], name: 'Profile', icon: 'fa-user'},
            {route: ['/School/Teacher/Student/Show', {id: id}], name: 'Activities', icon: 'fa-cogs'},
            {route: ['/School/Teacher/Student/Lessons', {id: id}], name: 'Lessons', icon: 'fa-book'}
        ];
    }
}