export class User {
    id: number;
    firstName: string;
    lastName: string;
    email: string;
    schoolName: string;
    settings: Object[];

    constructor(obj?: any) {
        this.id = obj && obj.id || 0;
        this.firstName = obj && obj.firstName || '';
        this.lastName = obj && obj.lastName || '';
        this.email = obj && obj.email || '';
        this.schoolName = obj && obj.schoolName || '';
        this.settings = obj && obj.settings || null;
    }

    fullName() {
        return this.firstName + ' ' + this.lastName;
    }
}