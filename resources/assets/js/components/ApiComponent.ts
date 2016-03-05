export class ApiComponent {
    data: any;
    isLoading: boolean = true;
    hasError: boolean = false;

    subscribeToSource(source) {
        source.subscribe(
            data => {
                this.hasError = false;
                this.isLoading = false;
                this.data = data;
            },
            () => {
                this.isLoading = false;
                this.hasError = true;
            }
        );
    }
}