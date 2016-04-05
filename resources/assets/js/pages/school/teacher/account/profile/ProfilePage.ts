import {Component} from 'angular2/core';

import {Profile} from "../../../../../components/school/teacher/profile/Profile";

@Component({
    selector: 'profile-page',
    directives: [Profile],
    template: require('./profilePage.html')
})
export class ProfilePage {

}