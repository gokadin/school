import {Component} from 'angular2/core';

require('./modal.scss');

@Component({
    selector: 'modal',
    template: require('./modal.html')
})
export class Modal {
    show: boolean;

    open(): void {
        console.log('show pressed');
        document.body.style.overflow = 'hidden';
        this.show = true;
    }

    cancel(): void {
        console.log('cancel pressed');
        document.body.style.overflow = 'auto';
        this.show = false;
    }

    cancelWithClick(e): void {
        if ((e.target.className).indexOf('modal-veil') == -1) {
            return;
        }

        this.cancel();
    }
//},
//    ready: function() {
//    window.addEventListener('keyup', this.handleKeyUp);
//
//    handleKeyUp: function(e) {
//    if (this.show && e.keyCode == 27) {
//        this.close();
//    }
//}
//}
}