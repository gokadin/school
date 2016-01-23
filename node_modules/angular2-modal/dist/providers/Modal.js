var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};
var __metadata = (this && this.__metadata) || function (k, v) {
    if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};
var __param = (this && this.__param) || function (paramIndex, decorator) {
    return function (target, key) { decorator(target, key, paramIndex); }
};
var core_1 = require('angular2/core');
var ModalConfig_1 = require('../models/ModalConfig');
var ModalDialogInstance_1 = require('../models/ModalDialogInstance');
var modalBackdrop_1 = require('../components/modalBackdrop');
var bootstrapModalContainer_1 = require('../components/bootstrapModalContainer');
var _config;
var Modal = (function () {
    function Modal(componentLoader, appRef, defaultConfig) {
        this.componentLoader = componentLoader;
        this.appRef = appRef;
        // The Modal class should be an application wide service (i.e: singleton).
        // This will run once in most applications...
        // If the user provides a ModalConfig instance to the DI,
        // the custom config will be the default one.
        _config = (defaultConfig) ? ModalConfig_1.ModalConfig.makeValid(defaultConfig) : new ModalConfig_1.ModalConfig();
    }
    /**
     * Opens a modal window blocking the whole screen.
     * @param componentType The angular Component to render as modal.
     * @param bindings Resolved providers that will inject into the component provided.
     * @param config A Modal Configuration object.
     * @returns {Promise<ModalDialogInstance>}
     */
    Modal.prototype.open = function (componentType, bindings, config) {
        // TODO: appRef.injector.get(APP_COMPONENT) Doesn't work.
        // When it does replace with the hack below.
        //let myElementRef = this.appRef.injector.get(APP_COMPONENT).location;
        var elementRef = this.appRef['_rootComponents'][0].location;
        return this.openInside(componentType, elementRef, null, bindings, config);
    };
    /**
     * Opens a modal window inside an existing component.
     * @param componentType The angular Component to render as modal.
     * @param elementRef The element to block using the modal.
     * @param anchorName A template variable within the component.
     * @param bindings Resolved providers that will inject into the component provided.
     * @param config A Modal Configuration object.
     * @returns {Promise<ModalDialogInstance>}
     */
    Modal.prototype.openInside = function (componentType, elementRef, anchorName, bindings, config) {
        var _this = this;
        config = (config) ? ModalConfig_1.ModalConfig.makeValid(config, _config) : _config;
        var dialog = new ModalDialogInstance_1.ModalDialogInstance(config);
        dialog.inElement = !!anchorName;
        var dialogBindings = core_1.Injector.resolve([core_1.provide(ModalDialogInstance_1.ModalDialogInstance, { useValue: dialog })]);
        return this.createBackdrop(elementRef, dialogBindings, anchorName)
            .then(function (backdropRef) {
            dialog.backdropRef = backdropRef;
            var modalDataBindings = core_1.Injector.resolve([core_1.provide(ModalDialogInstance_1.ModalDialogInstance, { useValue: dialog })]).concat(bindings);
            return _this.componentLoader.loadIntoLocation(bootstrapModalContainer_1.BootstrapModalContainer, backdropRef.location, 'modalBackdrop', dialogBindings)
                .then(function (bootstrapRef) {
                dialog.bootstrapRef = bootstrapRef;
                return _this.componentLoader.loadIntoLocation(componentType, bootstrapRef.location, 'modalDialog', modalDataBindings)
                    .then(function (contentRef) {
                    dialog.contentRef = contentRef;
                    return dialog;
                });
            });
        });
    };
    /**
     * Creates backdrop element.
     * @param {ElementRef} The element to block using the modal.
     * @param {ResolvedProvider[]} Resolved providers,
     *     must contain the ModalDialogInstance instance for this backdrop.
     * @param {string} An anchor name, optional.
     *     if not supplied backdrop gets applied next to elementRef, otherwise into it.
     * @returns {Promise<ComponentRef>}
     */
    Modal.prototype.createBackdrop = function (elementRef, bindings, anchorName) {
        return (!anchorName) ?
            this.componentLoader.loadNextToLocation(modalBackdrop_1.ModalBackdrop, elementRef, bindings) :
            this.componentLoader.loadIntoLocation(modalBackdrop_1.ModalBackdrop, elementRef, anchorName, bindings);
    };
    Modal = __decorate([
        core_1.Injectable(),
        __param(2, core_1.Optional()), 
        __metadata('design:paramtypes', [core_1.DynamicComponentLoader, core_1.ApplicationRef, ModalConfig_1.ModalConfig])
    ], Modal);
    return Modal;
})();
exports.Modal = Modal;
//# sourceMappingURL=Modal.js.map