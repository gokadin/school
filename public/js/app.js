Vue.http.headers.common['CSRFTOKEN'] = document.getElementById('csrf-token').getAttribute('content');

Vue.component('activities', {
    template: '#activities-template',

    data: function() {
        return {
            activities: []
        };
    },

    created: function() {
        this.$http.post('/api/school/teacher-activities', {
            page: 0,
            max: 10,
            sortBy: 'name',
            sortAscending: true,
            filters: {}
        }, function(activities) {
            this.activities = activities;
        });
    },

    methods: {
        deleteActivity: function(activity) {
            this.activities.$remove(activity);
        }
    }
});

new Vue({
    el: 'body'
});