import moment = require('moment');
import Moment = moment.Moment;

export class Availability {
    id: number;
    date: Moment;
    startTime: number;
    endTime: number;

    constructor(obj?: any) {
        this.id = obj && obj.id || 0;
        this.date = obj && moment(obj.date) || null;
        this.startTime = obj && obj.startTime || 100;
        this.endTime = obj && obj.endTime || 100;
    }
}