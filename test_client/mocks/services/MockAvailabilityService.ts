import {SpyObject} from 'angular2/testing_internal';

import {AvailabilityService} from "../../../resources/assets/js/services/AvailabilityService";

export class MockAvailabilityService extends SpyObject {
    fakeResponse;
    availabilities;
    fetch;
    store;
    update;
    delete;

    constructor() {
        super(AvailabilityService);

        this.fakeResponse = [];
        this.availabilities = {
            subscribe: (callback) => callback(this.fakeResponse)
        };
        this.fetch = this.spy('fetch').andReturn(this);
        this.store = this.spy('store').andReturn(this);
        this.update = this.spy('update').andReturn(this);
        this.delete = this.spy('delete').andReturn(this);
    }

    setResponse(json: any): void {
        this.fakeResponse = json;
    }
}