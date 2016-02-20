import {Component, Input, OnInit} from 'angular2/core';
import {AbstractControl} from 'angular2/common';

require('./datepicker.scss');

@Component({
    selector: 'datepicker',
    template: require('./datepicker.html')
})
export class Datepicker {
    @Input() control: AbstractControl;
    @Input() placeholder: string;
    show: boolean;

    open() {
        if (this.show) {
            return;
        }

        this.show = true;
    }

    close() {
        if (!this.show) {
            return;
        }

        this.show = false;
    }

    preventDefaultt(e) { // NOT WORKING...
        console.log('stopping propagation');
        e.stopPropagation();
    }
}