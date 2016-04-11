import {it, describe, expect, inject, fakeAsync,
    afterEach, beforeEach, tick} from 'angular2/testing';
import moment = require('moment');

import {MockEventService} from "../../mocks/services/MockEventService";
import {MockAvailabilityService} from "../../mocks/services/MockAvailabilityService";
import {Calendar} from "../../../resources/assets/js/components/school/calendar/Calendar";
import {Availability} from "../../../resources/assets/js/models/Availability";

describe('availabilities', () => {
    test = (name: string, callback: any) => it(name, callback);

    let calendar = null;
    let mockEventService = null;
    let mockAvailabilityService = null;

    beforeEach(() => {
        mockEventService = new MockEventService();
        mockAvailabilityService = new MockAvailabilityService();
        calendar = new Calendar(mockEventService, mockAvailabilityService);
    });

    test('should switch mode when entering availability mode', () => {
        // Act
        calendar.enterAvailabilityMode();

        // Assert
        expect(calendar.mode).toBe('availability');
    });

    test('should load week view when entering availability mode and week view is not loaded', () => {
        // Assert
        expect(calendar.weekViewLoaded).toBe(false);

        // Act
        calendar.enterAvailabilityMode();

        // Assert
        expect(calendar.weekViewLoaded).toBe(true);
    });

    test('should switch to week mode when exiting availability mode', () => {
        // Act
        calendar.enterAvailabilityMode();

        // Assert
        expect(calendar.mode).toBe('availability');

        // Act
        calendar.exitAvailabilityMode();

        // Assert
        expect(calendar.mode).toBe('week');
    });

    test('isTimeAvailable should return false if there is no data', () => {
        // Act
        let result = calendar.isTimeAvailable(0, 0, 0);

        // Assert
        expect(result).toBe(false);
    });

    test('isTimeAvailable should return false if there is only an availability ending before the start time', () => {
        // Arrange
        calendar.currentRow = 0;
        calendar.dates[0].availabilities.push(new Availability({
            id: 1, date: moment(), startTime: 200, endTime: 300
        }));

        // Act
        let result = calendar.isTimeAvailable(0, 300, 400);

        // Assert
        expect(result).toBe(false);
    });

    test('isTimeAvailable should return false if there is only an availability starting after the end time', () => {
        // Arrange
        calendar.currentRow = 0;
        calendar.dates[0].availabilities.push(new Availability({
            id: 1, date: moment(), startTime: 400, endTime: 500
        }));

        // Act
        let result = calendar.isTimeAvailable(0, 300, 400);

        // Assert
        expect(result).toBe(false);
    });

    test('isTimeAvailable should return true if there is an availability overlapping the given times', () => {
        // Arrange
        calendar.currentRow = 0;
        calendar.dates[0].availabilities.push(new Availability({
            id: 1, date: moment(), startTime: 399, endTime: 500
        }));

        // Act
        let result = calendar.isTimeAvailable(0, 300, 400);

        // Assert
        expect(result).toBe(true);
    });

    test('createAvailability should not create an availability if the time is already available', () => {
        // Arrange
        calendar.currentRow = 0;
        calendar.dates[0].availabilities.push(new Availability({
            id: 1, date: moment(), startTime: 300, endTime: 450
        }));

        // Act
        calendar.createAvailability(0, 400, 500);

        // Assert
        expect(mockAvailabilityService.stubMethods.store.callCount).toBe(0);
    });

    test('createAvailability should store the availability if the time is not already available', () => {
        // Arrange
        calendar.currentRow = 0;
        calendar.dates[0].availabilities.push(new Availability({
            id: 1, date: moment(), startTime: 300, endTime: 400
        }));

        // Act
        calendar.createAvailability(0, 400, 500);

        // Assert
        expect(mockAvailabilityService.stubMethods.store.callCount).toBe(1);
    });
});