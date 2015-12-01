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
            searchFields: {},
            searchDelayTimer: null,
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

            if (value == '') {
                delete(this.searchFields.name);
            } else {
                this.searchFields.name = value;
            }

            this.doSearch();
        },

        'searchRate': function(value, oldValue) {
            if (value.trim() == oldValue.trim()) {
                return;
            }

            if (value == '') {
                delete(this.searchFields.rate);
            } else {
                this.searchFields.rate = value;
            }

            this.doSearch();
        },

        'searchPeriod': function(value, oldValue) {
            if (value.trim() == oldValue.trim()) {
                return;
            }

            if (value == '') {
                delete(this.searchFields.period);
            } else {
                this.searchFields.period = value;
            }

            this.doSearch();
        }
    },

    created: function() {
        this.$http.post('/api/school/teacher-activities', {
            page: this.page,
            max: this.max,
            sortBy: this.sortBy,
            sortAscending: this.sortAscending,
            filters: {}
        }, function(response) {
            this.activities = response.activities;
            this.total = response.totalCount;
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
            }, function(response) {
                this.activities = response.activities;
                this.total = response.totalCount;
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
            }, function(response) {
                this.activities = response.activities;
                this.total = response.totalCount;
                this.page++;
            });
        },

        doSearch: function() {
            clearTimeout(this.searchDelayTimer);
            this.searchDelayTimer = setTimeout(function() {
                this.page = 0;
                this.$http.post('/api/school/teacher-activities', {
                    page: this.page,
                    max: this.max,
                    sortBy: this.sortBy,
                    sortAscending: this.sortAscending,
                    filters: this.searchFields
                }, function(response) {
                    this.activities = response.activities;
                    this.total = response.totalCount;
                });
            }.bind(this), 200);
        }
    }
});

new Vue({
    el: 'body'
});