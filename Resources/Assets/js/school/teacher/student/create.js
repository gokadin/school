module.exports = {
    template: '#create-template',

    data: function() {
        return {
            activities: activities,
            selectedActivity: 0,
            rate: 0
        }
    },

    ready: function() {
        if (activities.length == 0) {
            return;
        }

        this.selectedActivity = this.activities[0].id;
        this.rate = this.activities[0].rate;
    },

    methods: {
        selectedActivityChanged: function() {
            var selectedValue = this.selectedActivity;
            var rate = 0;

            $.each(activities, function(index, value) {
                if (value.id == selectedValue) {
                    rate = value.rate;
                    return false;
                }
            });

            this.rate = rate;
        }
    }
};