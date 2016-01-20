export class Event {
    id: number;
    title: string;

    constructor(obj?: any) {
        this.id = obj && obj.id || 0;
        this.title = obj && obj.title || '';
    }
}