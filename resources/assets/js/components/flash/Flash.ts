 import {Component, Injectable, provide} from 'angular2/core';

require('./flash.scss');

@Component({
    selector: 'flash',
    template: require('./flash.html')
})
@Injectable()
export class Flash {
    _show: boolean;

    show() {
        this._show = true;
    }

    close() {
        this._show = false;
    }
}

export var FLASH_PROVIDERS = [
    provide(Flash, {useClass: Flash})
];