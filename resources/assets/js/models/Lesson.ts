import Moment = moment.Moment;
import moment = require("moment");

export class Lesson {
    id: number;
    eventId: number;
    title: string;
    startDate: Moment;
    endDate: Moment;
    startTime: string;
    endTime: string;
    attended: boolean;

    constructor(obj: Object) {
        console.log(obj);
        this.id = obj.id;
        this.eventId = obj.eventId;
        this.title = obj.title;
        this.startDate = moment(obj.startDate);
        this.endDate = moment(obj.endDate);
        this.startTime = obj.startTime;
        this.endTime = obj.endTime;
        this.attended = obj.attended;
    }
}