import {MockService} from "../../helpers/MockService";
import Moment = moment.Moment;

import {Availability} from "../../../resources/assets/js/models/Availability";

export class MockAvailabilityService extends MockService {
    availabilities: Object;

    constructor() {
        super();

        this.availabilities = this.getObservableProperty();

        this.registerStubMethod('fetch');
        this.registerStubMethod('store');
        this.registerStubMethod('update');
        this.registerStubMethod('delete');
    }

    fetch(from: Moment, to: Moment): void {
        this.recordCall('fetch', [from, to]);
    }

    store(availability: Availability): void {
        this.recordCall('store', [availability]);
    }

    update(availability: Availability): void {
        this.recordCall('update', [availability]);
        return this.getObservableProperty();
    }

    delete(availability: Availability): void {
        this.recordCall('delete', [availability]);
        return this.getObservableProperty();
    }
}