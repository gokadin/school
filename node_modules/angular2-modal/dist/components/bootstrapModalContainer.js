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
 * A component that acts as a top level container for an open modal window.
 */
var BootstrapModalContainer = (function () {
    function BootstrapModalContainer(dialogInstance) {
        this.dialogInstance = dialogInstance;
        if (!dialogInstance.inElement) {
            this.position = null;
        }
        else {
            this.position = 'absolute';
        }
    }
    BootstrapModalContainer.prototype.onContainerClick = function ($event) {
        $event.stopPropagation();
    };
    BootstrapModalContainer.prototype.onClick = function () {
        return !this.dialogInstance.config.isBlocking && this.dialogInstance.dismiss();
    };
    BootstrapModalContainer.prototype.documentKeypress = function (event) {
        if (this.dialogInstance.config.keyboard &&
            this.dialogInstance.config.keyboard.indexOf(event.keyCode) > -1) {
            this.dialogInstance.dismiss();
        }
    };
    BootstrapModalContainer = __decorate([
        core_1.Component({
            selector: 'bootstrap-modal',
            host: {
                'tabindex': '0',
                'role': 'dialog',
                'class': 'in modal',
                'style': 'display: block',
                '[style.position]': 'position',
                '(body:keydown)': 'documentKeypress($event)',
                '(click)': 'onClick()'
            },
            /* tslint:disable */
            template: "<div class=\"modal-dialog\"\n         [class.modal-lg]=\"dialogInstance.config.size == 'lg'\"\n         [class.modal-sm]=\"dialogInstance.config.size == 'sm'\">\n         <div class=\"modal-content\" (click)=\"onContainerClick($event)\" style=\"display: block\">\n            <div style=\"display: none\" #modalDialog></div>\n         </div>\n    </div>"
        }), 
        __metadata('design:paramtypes', [ModalDialogInstance_1.ModalDialogInstance])
    ], BootstrapModalContainer);
    return BootstrapModalContainer;
})();
exports.BootstrapModalContainer = BootstrapModalContainer;
//# sourceMappingURL=bootstrapModalContainer.js.map