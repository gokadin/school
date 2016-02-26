import moment = require('moment');
import Moment = moment.Moment;

export class Student {
    id: number;
    firstName: string;
    lastName: number;
    fullName: string;
    email: number;
    activityName: string;
    dateRegistered: Moment;
    active: boolean;

    constructor(obj?: any) {
        this.id = obj && obj.id || 0;
        this.firstName = obj && obj.firstName || '';
        this.lastName = obj && obj.lastName || 0;
        this.fullName = obj && obj.fullName || '';
        this.email = obj && obj.email || 0;
        this.activityName = obj && obj.activityName || '';
        this.dateRegistered = obj && moment(obj.createdAt.date) || null;
        this.active = obj && obj.active || false;
    }
}