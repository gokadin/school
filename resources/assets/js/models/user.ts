export class User {
    id: number;
    firstName: string;
    lastName: string;
    email: string;
    schoolName: string;

    constructor(obj?: any) {
        this.id = obj && obj.id || 0;
        this.firstName = obj.firstName || '';
        this.lastName = obj.lastName || '';
        this.email = obj.email || '';
        this.schoolName = obj.schoolName || '';
    }

    fullName() {
        return this.firstName + ' ' + this.lastName;
    }
}