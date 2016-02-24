import {Component} from 'angular2/core';
import {FORM_DIRECTIVES, FormBuilder, Validators, AbstractControl} from 'angular2/common';
import {Http} from 'angular2/http';
import {Router} from 'angular2/router';

import {FormComponent} from "../../../FormComponent";

@Component({
    selector: 'create-student-form',
    directives: [FORM_DIRECTIVES],
    template: require('./createStudentForm.html')
})
export class CreateStudentForm extends FormComponent {
    createAnother: boolean;
    firstName: AbstractControl;
    lastName: AbstractControl;
    email: AbstractControl;

    constructor(fb: FormBuilder, http: Http, private router:Router) {
        super('/api/school/teacher/students/', http);

        this.form = fb.group({
            'firstName': [''],
            'lastName': [''],
            'email': ['', Validators.required]
        });

        this.firstName = this.form.controls['firstName'];
        this.lastName = this.form.controls['lastName'];
        this.email = this.form.controls['email'];
    }

    afterSubmit(): void {
        if (!this.createAnother) {
            this.router.navigate(['/School/Teacher/Student/Index']);

            return;
        }

        // ...
    }
}