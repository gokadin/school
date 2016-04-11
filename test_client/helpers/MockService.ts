export class MockService {
    fakeResponse: any;
    stubMethods: Object;

    constructor() {
        this.fakeResponse = [];
        this.stubMethods = {};
    }

    setResponse(response: any): void {
        this.fakeResponse = response;
    }

    getObservableProperty(): void {
        return {
            subscribe: (callback) => callback(this.fakeResponse)
        };
    }

    registerStubMethod(methodName: string): void {
        this.stubMethods[methodName] = {
            callCount: 0,
            callArgs: []
        };
    }

    recordCall(methodName: string, args: any[]): void {
        this.stubMethods[methodName].callCount++;
        this.stubMethods[methodName].callArgs.push(args);
    }
}