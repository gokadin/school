var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};
var __metadata = (this && this.__metadata) || function (k, v) {
    if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};
var core_1 = require('angular2/core');
var ModalDialogInstance_1 = require('../models/ModalDialogInstance');
/**
 * Represents the modal backdrop.
 */
var ModalBackdrop = (function () {
    function ModalBackdrop(dialog) {
        if (!dialog.inElement) {
            this.position = this.width = this.height = null;
            this.top = this.left = this.right = this.bottom = null;
        }
        else {
            this.position = 'absolute';
            this.height = '100%';
            this.width = '100%';
            this.top = this.left = this.right = this.bottom = '0';
        }
    }
    ModalBackdrop = __decorate([
        core_1.Component({
            selector: 'modal-backdrop',
            host: {
                '[style.position]': 'position',
                '[style.height]': 'height',
                '[style.width]': 'width',
                '[style.top]': 'top',
                '[style.left]': 'left',
                '[style.right]': 'right',
                '[style.bottom]': 'bottom'
            },
            template: '<div [style.position]="position" class="in modal-backdrop" #modalBackdrop></div>'
        }), 
        __metadata('design:paramtypes', [ModalDialogInstance_1.ModalDialogInstance])
    ], ModalBackdrop);
    return ModalBackdrop;
})();
exports.ModalBackdrop = ModalBackdrop;
//# sourceMappingURL=modalBackdrop.js.map