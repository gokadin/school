export class Modal {
    show: boolean;

    open() {
        console.log('show pressed');
        this.show = true;
    }

    cancel() {
        console.log('cancel pressed');
        this.show = false;
    }
}