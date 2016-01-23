import moment = require('moment');
import Moment = moment.Moment;

export class Event {
    id: number;
    title: string;
    startDate: Moment;

    constructor(obj?: any) {
        this.id = obj && obj.id || 0;
        this.title = obj && obj.title || '';
        this.startDate = obj && moment(obj.startDate) || null;
    }
}