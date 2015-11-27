Vue.http.headers.common['CSRFTOKEN'] = document.getElementById('csrf-token').getAttribute('content');

Vue.component('activities', {
    template: '#activities-template',

    data: function() {
        return {
            activities: [],
            mainFilter: '',
            searchName: '',
            searchRate: '',
            searchPeriod: '',
            total: 0,
            sortBy: 'name',
            sortAscending: true,
            page: 0,
            max: 10
        };
    },

    computed: {
        hasPreviousPage: function() {
            return this.page > 0;
        },

        hasNextPage: function() {
            return this.page * this.max <= this.total - this.max;
        }
    },

    watch: {
        'searchName': function(value, oldValue) {
            if (value.trim() == oldValue.trim()) {
                return;
            }

            this.$http.post('/api/school/teacher-activities', {
                page: this.page,
                max: this.max,
                sortBy: this.sortBy,
                sortAscending: this.sortAscending,
                filters: {
                    'name': value
                }
            }, function(activities) {
                this.activities = activities;
            });
        }
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
        previousPage: function() {
            if (!this.hasPreviousPage) {
                return;
            }

            this.$http.post('/api/school/teacher-activities', {
                page: this.page - 1,
                max: this.max,
                sortBy: this.sortBy,
                sortAscending: this.sortAscending,
                filters: {}
            }, function(activities) {
                this.activities = activities;
                this.page--;
            });
        },

        nextPage: function() {
            if (!this.hasNextPage) {
                return;
            }

            this.$http.post('/api/school/teacher-activities', {
                page: this.page + 1,
                max: this.max,
                sortBy: this.sortBy,
                sortAscending: this.sortAscending,
                filters: {}
            }, function(activities) {
                this.activities = activities;
                this.page++;
            });
        }
    }
});

new Vue({
    el: 'body'
});