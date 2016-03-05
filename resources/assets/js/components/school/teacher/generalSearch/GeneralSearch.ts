import {Component} from 'angular2/core';
import {Control} from 'angular2/common';
import {ROUTER_DIRECTIVES, Router} from 'angular2/router';

import {SearchService} from "../../../../services/SearchService";
import {Student} from "../../../../models/Student";
import {Activity} from "../../../../models/Activity";

require('./generalSearch.scss');

@Component({
    selector: 'general-search',
    directives: [ROUTER_DIRECTIVES],
    template: require('./generalSearch.html')
})
export class GeneralSearch {
    show: boolean;
    touched: boolean;
    students: Student[] = [];
    activities: Activity[] = [];
    searchInput: Control;
    searchInputModel: string;
    selectedIndex: number;

    constructor(private searchService: SearchService, private router: Router) {
        this.searchInput = new Control();
        this.selectedIndex = -1;

        this.searchService.generalResults.subscribe(
            data => {
                this.students = data.students;
                this.activities = data.activities;
            }
        );

        this.searchInput.valueChanges
            .filter(value => value != undefined)
            .debounceTime(400)
            .distinctUntilChanged()
            .subscribe(
                value => this.handleInputChanged(value)
            );
    }

    handleInputChanged(value: string): void {
        value == '' ? this.clearData() : this.searchService.fetchGeneralResults(value);

        if (!this.touched) {
            this.touched = true;
        }

        if (!this.show && value != '') {
            this.show = true;
        }

        this.selectedIndex = -1;
    }

    totalResults(): number {
        return this.students.length + this.activities.length;
    }

    open(): void {
        this.selectedIndex = -1;
        this.show = true;
        this.touched = this.searchInput.value && this.searchInput.value != '';
    }

    close(): void {
        this.show = false;
    }

    keepOpen(e): void {
        console.log('ok');
        e.stopPropagation();
    }

    haveData(): boolean {
        return this.students.length > 0 || this.activities.length > 0;
    }

    clearData(): void {
        this.students = [];
        this.activities = [];
    }

    handleKeyPress(keyCode: number): void {
        switch (keyCode) {
            case 27:
                this.handleEscape();
                break;
            case 13:
                this.handleEnter();
                break;
            case 38:
                this.handleUp();
                break;
            case 40:
                this.handleDown();
                break;
        }
    }

    handleDown(): void {
        if (!this.show || !this.touched || this.selectedIndex >= this.totalResults() - 1) {
            return;
        }

        this.selectedIndex++;

        this.fixResultScroll();
    }

    handleUp(): void {
        if (!this.show || !this.touched || this.selectedIndex <= 0) {
            return;
        }

        this.selectedIndex--;

        this.fixResultScroll();
    }

    handleEnter(): void {
        if (!this.show || !this.touched || this.totalResults() == 0) {
            return;
        }

        if (this.selectedIndex < 0 || this.selectedIndex > this.totalResults() - 1) {
            if (this.students.length > 0) {
                this.router.navigate(['/School/Teacher/Student/Show', {id: this.students[0].id}]);
            } else {
                this.router.navigate(['/School/Teacher/Activity/Index']);
            }

            this.close();
            this.searchInputModel = '';

            return;
        }

        if (this.selectedIndex < this.students.length) {
            this.router.navigate(['/School/Teacher/Student/Show', {id: this.students[this.selectedIndex].id}]);
        } else {
            this.router.navigate(['/School/Teacher/Activity/Index']);
        }

        this.close();
        this.searchInputModel = '';
    }

    handleEscape(): void {
        this.close();
    }

    fixResultScroll(): void {
        let el = document.getElementById('general-search-results');

        let selectedPosition = 0;
        if (this.selectedIndex < this.students.length || this.students.length == 0) {
            selectedPosition = (this.selectedIndex + 1) * 30 + 22;
        } else {
            selectedPosition = (this.selectedIndex + 1) * 30 + 44;
        }

        if (selectedPosition > el.scrollTop + 400) {
            el.scrollTop = selectedPosition - 400;
        } else if (selectedPosition <= el.scrollTop + 22) {
            el.scrollTop = this.selectedIndex * 30 + 22;
        }
    }

    routeToStudent(student: Student): void {
        this.router.navigate(['/School/Teacher/Student/Show', {id: student.id}]);
        this.close();
        this.searchInputModel = '';
    }

    routeToActivities(): void {
        this.router.navigate(['/School/Teacher/Activity/Index']);
        this.close();
        this.searchInputModel = '';
    }

    ignoreClick(e): void {
        e.preventDefault();
        e.stopPropagation();
    }
}