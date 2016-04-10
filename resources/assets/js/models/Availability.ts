import moment = require('moment');
import Moment = moment.Moment;

export class Availability {
    id: number;
    date: Moment;
    startTime: number;
    endTime: number;
    originalHeight: number;
    height: number;
    position: number;

    constructor(obj?: any) {
        this.id = obj && obj.id || 0;
        this.date = obj && moment(obj.date) || null;
        this.startTime = obj && parseInt(obj.startTime) || 100;
        this.endTime = obj && parseInt(obj.endTime) || 100;

        this.height = (this.endTime - this.startTime) / 25 * 12;
        this.originalHeight = this.height;
        this.position = this.startTime / 25 * 12 - 48;
    }
}