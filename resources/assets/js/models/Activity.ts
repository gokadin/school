export class Activity {
    id: number;
    name: string;
    rate: number;
    period: string;
    studentCount: number;

    constructor(obj?: any) {
        this.id = obj && obj.id || 0;
        this.name = obj && obj.name || '';
        this.rate = obj && obj.rate || 0;
        this.period = obj && obj.period || '';
        this.studentCount = obj && obj.studentCount || 0;
    }
}