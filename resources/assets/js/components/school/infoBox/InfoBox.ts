import {Component} from 'angular2/core';

require('./infoBox.scss');

import {AuthService} from "../../../services/authService";

@Component({
    selector: 'info-box',
    template: require('./infoBox.html')
})
export class InfoBox {
    show: boolean;

    constructor(authService: AuthService) {
        authService.user.subscribe(
            x => this.show = x.showTips
        );
    }

    close() {
        this.show = false;
    }
}