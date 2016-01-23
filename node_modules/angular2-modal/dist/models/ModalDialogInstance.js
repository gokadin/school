var async_1 = require('angular2/src/facade/async');
/**
 * API to an open modal window.
 */
var ModalDialogInstance = (function () {
    function ModalDialogInstance(config) {
        this.config = config;
        this._resultDefered = async_1.PromiseWrapper.completer();
    }
    Object.defineProperty(ModalDialogInstance.prototype, "backdropRef", {
        set: function (value) {
            this._backdropRef = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(ModalDialogInstance.prototype, "bootstrapRef", {
        set: function (value) {
            this._bootstrapRef = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(ModalDialogInstance.prototype, "result", {
        /**
         * A Promise that is resolved on a close event and rejected on a dismiss event.
         * @returns {Promise<T>|any|*|Promise<any>}
         */
        get: function () {
            return this._resultDefered.promise;
        },
        enumerable: true,
        configurable: true
    });
    /**
     *  Close the modal with a return value, i.e: result.
     */
    ModalDialogInstance.prototype.close = function (result) {
        if (result === void 0) { result = null; }
        if (this.contentRef.instance.beforeClose &&
            this.contentRef.instance.beforeClose() === true)
            return;
        this.dispose();
        this._resultDefered.resolve(result);
    };
    /**
     *  Close the modal without a return value, i.e: cancelled.
     *  This call is automatically invoked when a user either:
     *  - Presses an exit keyboard key (if configured).
     *  - Clicks outside of the modal window (if configured).
     *  Usually, dismiss represent a Cancel button or a X button.
     */
    ModalDialogInstance.prototype.dismiss = function () {
        if (this.contentRef.instance.beforeDismiss &&
            this.contentRef.instance.beforeDismiss() === true)
            return;
        this.dispose();
        this._resultDefered.reject();
    };
    ModalDialogInstance.prototype.dispose = function () {
        this._bootstrapRef.dispose();
        this._backdropRef.dispose();
        this.contentRef.dispose();
    };
    return ModalDialogInstance;
})();
exports.ModalDialogInstance = ModalDialogInstance;
//# sourceMappingURL=ModalDialogInstance.js.map