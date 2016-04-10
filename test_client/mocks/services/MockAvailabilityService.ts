import {provide} from 'angular2/core';

import {AvailabilityService} from "../../../resources/assets/js/services/AvailabilityService";

export class MockAvailabilityService extends SpyObject {
    fakeResponse;
    fetch;
    store;
    update;
    delete;

    constructor() {
        super(AvailabilityService);

        this.fakeResponse = null;
        this.fetch = this.spy('fetch').andReturn(this);
        this.store = this.spy('store').andReturn(this);
        this.update = this.spy('update').andReturn(this);
        this.delete = this.spy('delete').andReturn(this);
    }

    subscribe(callback) {
        callback(this.fakeResponse);
    }

    setResponse(json: any): void {
        this.fakeResponse = json;
    }

    getProviders(): Array<any> {
        return [provide(AvailabilityService, {useClass: this})]
    }
}