import moment = require('moment');
import Moment = moment.Moment;

export class Event {
    id: number;
    title: string;
    description: string;
    startDate: Moment;
    endDate: Moment;
    startTime: string;
    endTime: string;
    isAllDay: boolean;
    isRecurring: boolean;
    rRepeat: string;
    rEvery: string;
    rEndDate: Moment;
    rEndsNever: boolean;
    color: string;
    notifyMeBy: string;
    notifyMeBefore: string;
    location: string;
    visibility: string;
    activityId: number;

    constructor(obj?: any) {
        this.id = obj && obj.id || 0;
        this.title = obj && obj.title || '';
        this.startDate = obj && moment(obj.startDate) || null;
    }
}