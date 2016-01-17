import {Component} from 'angular2/core';
import {bootstrap} from 'angular2/platform/browser';

require('./../sass/app.scss');

@Component({
    selector: 'app',
    template: require('./app.html')
})
class App {

}

bootstrap(App);