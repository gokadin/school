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
var common_1 = require('angular2/common');
var ICustomModal_1 = require('../models/ICustomModal');
var ModalDialogInstance_1 = require('../models/ModalDialogInstance');
/**
 * Data definition
 */
var YesNoModalContent = (function () {
    function YesNoModalContent(title, body, hideNo, yesText, noText) {
        if (title === void 0) { title = 'Hello World Title'; }
        if (body === void 0) { body = 'Hello World Body!'; }
        if (hideNo === void 0) { hideNo = false; }
        if (yesText === void 0) { yesText = 'YES'; }
        if (noText === void 0) { noText = 'NO'; }
        this.title = title;
        this.body = body;
        this.hideNo = hideNo;
        this.yesText = yesText;
        this.noText = noText;
    }
    return YesNoModalContent;
})();
exports.YesNoModalContent = YesNoModalContent;
/**
 * A 2 state bootstrap modal window, representing 2 possible answer, true/false.
 */
var YesNoModal = (function () {
    function YesNoModal(dialog, modelContentData) {
        this.dialog = dialog;
        this.context = modelContentData;
    }
    YesNoModal.prototype.ok = function ($event) {
        $event.stopPropagation();
        this.dialog.close(true);
    };
    YesNoModal.prototype.cancel = function () {
        this.dialog.dismiss();
    };
    YesNoModal = __decorate([
        core_1.Component({
            selector: 'modal-content',
            directives: [common_1.NgIf],
            /* tslint:disable */ template: "<div class=\"modal-header\">\n        <h3 class=\"modal-title\">{{context.title}}</h3>\n        </div>\n        <div class=\"modal-body\">{{context.body}}</div>\n        <div class=\"modal-footer\">\n            <button class=\"btn btn-primary\" (click)=\"ok($event)\">{{context.yesText}}</button>\n            <button *ngIf=\"!context.hideNo\" class=\"btn btn-warning\" (click)=\"cancel()\">{{context.noText}}</button>\n        </div>"
        }), 
        __metadata('design:paramtypes', [ModalDialogInstance_1.ModalDialogInstance, ICustomModal_1.ICustomModal])
    ], YesNoModal);
    return YesNoModal;
})();
exports.YesNoModal = YesNoModal;
//# sourceMappingURL=yesNoModal.js.map