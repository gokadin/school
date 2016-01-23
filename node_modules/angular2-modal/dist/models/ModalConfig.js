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
var _defaultConfig;
/**
 * A configuration definition object.
 * Instruction for how to show a modal.
 */
var ModalConfig = (function () {
    function ModalConfig(size, isBlocking, keyboard) {
        if (size === void 0) { size = null; }
        if (isBlocking === void 0) { isBlocking = null; }
        if (keyboard === void 0) { keyboard = undefined; }
        this.size = size;
        this.isBlocking = isBlocking;
        this.keyboard = keyboard;
    }
    /**
     * Makes a ModalConfig instance valdud.
     * @param config
     * @param defaultConfig A Default config to use as master, optional.
     * @returns {ModalConfig} The same config instance sent.
     */
    ModalConfig.makeValid = function (config, defaultConfig) {
        defaultConfig = (defaultConfig) ? defaultConfig : _defaultConfig;
        if (!config.size)
            config.size = defaultConfig.size;
        if (config.isBlocking !== false)
            config.isBlocking = true;
        if (config.keyboard !== null) {
            if (Array.isArray(config.keyboard)) {
                config.keyboard = config.keyboard.length === 0
                    ? defaultConfig.keyboard : config.keyboard;
            }
            else if (!isNaN(config.keyboard)) {
                config.keyboard = [config.keyboard];
            }
            else {
                config.keyboard = defaultConfig.keyboard;
            }
        }
        return config;
    };
    ModalConfig = __decorate([
        core_1.Injectable(), 
        __metadata('design:paramtypes', [String, Boolean, Object])
    ], ModalConfig);
    return ModalConfig;
})();
exports.ModalConfig = ModalConfig;
_defaultConfig = new ModalConfig('lg', true, [27]);
//# sourceMappingURL=ModalConfig.js.map