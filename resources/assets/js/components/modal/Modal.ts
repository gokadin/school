export class Modal {
    show: boolean;

    constructor() {
        this.show = false;
    }

    ngOnInit() {
        window.addEventListener('keypress', e => this.handleKeyPress(e));
    }

    open(): void {
        document.body.style.overflow = 'hidden';
        this.show = true;
    }

    cancel(): void {
        if (!this.show) {
            return;
        }

        document.body.style.overflow = 'auto';
        this.show = false;
    }

    cancelWithClick(e): void {
        if ((e.target.className).indexOf('modal-veil') == -1) {
            return;
        }

        this.cancel();
    }

    handleKeyPress(e): void {
        if (e.keyCode == 27) {
            this.cancel();
        }
    }
}