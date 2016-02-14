import {OnInit} from 'angular2/core';

export class Modal implements OnInit {
    show: boolean;

    constructor() {
        this.show = false;
    }

    ngOnInit() {
        window.addEventListener('keyup', this.handleKeyUp); // not working
    }

    open(): void {
        document.body.style.overflow = 'hidden';
        this.show = true;
    }

    cancel(): void {
        document.body.style.overflow = 'auto';
        this.show = false;
    }

    cancelWithClick(e): void {
        if ((e.target.className).indexOf('modal-veil') == -1) {
            return;
        }

        this.cancel();
    }

    handleKeyUp(e): void {
        console.log(this.show);
        if (this.show && e.keyCode == 27) {
            this.cancel();
        }
    }
}