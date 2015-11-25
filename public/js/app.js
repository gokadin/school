Vue.component('activities', {
    template: '#activities-template',

    props: ['activities'],

    created: function() {
        this.activities = JSON.parse(this.activities);
    }
});

new Vue({
    el: 'body'
});