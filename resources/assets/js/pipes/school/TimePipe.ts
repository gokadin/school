import {Pipe, PipeTransform} from 'angular2/core';

@Pipe({name: 'hour'})
export class TimePipe implements PipeTransform {
    transform(value: number): string {
        return value < 1300 ? value / 100 + 'am' : value / 100 + 'pm';
    }
}