import {it, describe, expect, inject, fakeAsync,
    afterEach, beforeEach, tick} from 'angular2/testing';

import {MockEventService} from "../../mocks/services/MockEventService";
import {MockAvailabilityService} from "../../mocks/services/MockAvailabilityService";
import {Calendar} from "../../../resources/assets/js/components/school/calendar/Calendar";

describe('availabilities', () => {
    let calendar = null;

    beforeEach(() => {
        calendar = new Calendar(new MockEventService(), new MockAvailabilityService());
    });

    it('should switch mode when entering availability mode', () => {
        // Act
        calendar.enterAvailabilityMode();

        // Assert
        expect(calendar.mode).toBe('availability');
    });

    it('should load week view when entering availability mode and week view is not loaded', () => {
        // Assert
        expect(calendar.weekViewLoaded).toBe(false);

        // Act
        calendar.enterAvailabilityMode();

        // Assert
        expect(calendar.weekViewLoaded).toBe(true);
    });

    it('should switch to week mode when exiting availability mode', () => {
        // Act
        calendar.enterAvailabilityMode();

        // Assert
        expect(calendar.mode).toBe('availability');

        // Act
        calendar.exitAvailabilityMode();

        // Assert
        expect(calendar.mode).toBe('week');
    });
});