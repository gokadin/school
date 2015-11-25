Vue.component('activities', {
    template: '#activities-template',

    data: function() {
        return {
            activities: []
        };
    },

    created: function() {
        $.getJSON('/api/school/user-activities', function(activities) {
            this.activities = activities;
        }.bind(this));
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