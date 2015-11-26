Vue.component('activities', {
    template: '#activities-template',

    data: function() {
        return {
            activities: []
        };
    },

    created: function() {
        this.$http.get('/api/school/user-activities', function(activities) {
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