import {FORM_DIRECTIVES, FormBuilder, ControlGroup, Validators} from 'angular2/common';
import {Http} from 'angular2/http';

export abstract class FormComponent {
    form: ControlGroup;
    submitEnabled: boolean = true;
    url: string;

    constructor(url: string, private http: Http) {
        this.url = url;
    }

    onSubmit(): void {
        if (!this.form.valid || !this.submitEnabled) {
            for (let key in this.form.controls) {
                if (this.form.controls.hasOwnProperty(key)) {
                    this.form.controls[key].markAsTouched();
                }
            }

            return;
        }

        this.submitEnabled = false;
        this.submit();
        this.afterSubmit();
    }

    submit(): void {
        this.http.post(this.url, JSON.stringify(this.form.value))
            .subscribe(
                () => {
                    this.submitEnabled = true;
                },
                () => {
                    this.submitEnabled = true;
                }
            );
    }

    afterSubmit(): void {}
}