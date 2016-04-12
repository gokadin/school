import {Pipe, PipeTransform} from 'angular2/core';

@Pipe({name: 'time'})
export class TimePipe implements PipeTransform {
    transform(value: number, [format]): string {
        switch (format) {
            case 'hour':
                return this.hour(value);
            case 'ampm':
                return this.ampm(value);
        }
    }

    hour(value: number): string {
        return value < 1300 ? value / 100 + 'am' : value / 100 - 12 + 'pm';
    }

    ampm(value: number): string {
        let hour = Math.floor(value / 100);
        let minute = (value % 100) / 25 * 15;
        if (minute == 0) {
            minute = '00';
        }

        return hour < 13 ? hour + ':' + minute + 'am' : hour - 12 + ':' + minute + 'pm';
    }
}