import {Component} from 'angular2/core';

import {Modal} from "../../../modal/Modal";

@Component({
    selector: 'new-event-modal',
    directives: [Modal],
    template: require('./newEventModal.html')
})
export class NewEventModal extends Modal {

}