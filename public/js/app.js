Vue.http.headers.common['CSRFTOKEN'] = document.getElementById('csrf-token').getAttribute('content');

Vue.component('activities', {
    template: '#activities-template',

    data: function() {
        return {
            activities: [],
            total: 0,
            sortBy: 'name',
            sortAscending: true,
            page: 0,
            max: 10
        };
    },

    created: function() {
        this.$http.post('/api/school/teacher-activities', {
            page: this.page,
            max: this.max,
            sortBy: this.sortBy,
            sortAscending: this.sortAscending,
            filters: {}
        }, function(activities) {
            this.activities = activities;
        });

        this.$http.get('/api/school/teacher-activities/total', function(total) {
            this.total = total;
        });
    },

    methods: {

    }
});

new Vue({
    el: 'body'
});