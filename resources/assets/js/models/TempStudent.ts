export class TempStudent {
    id: number;
    firstName: string;
    lastName: number;
    fullName: string;
    email: number;
    activityName: string;
    status: string;

    constructor(obj?: any) {
        this.id = obj && obj.id || 0;
        this.firstName = obj && obj.firstName || '';
        this.lastName = obj && obj.lastName || 0;
        this.email = obj && obj.email || 0;
        this.activityName = obj && obj.activityName || '';
        this.status = obj && obj.status || '';

        if (this.firstName && this.lastName) {
            this.fullName = this.firstName + ' ' + this.lastName;
        }
    }
}