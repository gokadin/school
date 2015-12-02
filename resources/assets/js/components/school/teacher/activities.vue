<script>
export default {
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
            sortFields: {
                name: 'asc',
                rate: 'none',
                period: 'none',
                students: 'none'
            },
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
        this.doRequest();
    },

    methods: {
        previousPage: function() {
            if (!this.hasPreviousPage) {
                return;
            }

            this.page--;

            this.doRequest();
        },

        nextPage: function() {
            if (!this.hasNextPage) {
                return;
            }

            this.page++;

            this.doRequest();
        },

        doSearch: function() {
            clearTimeout(this.searchDelayTimer);
            this.searchDelayTimer = setTimeout(function() {
                this.page = 0;

                this.doRequest();
            }.bind(this), 200);
        },

        sortBy: function(prop) {
            if (this.sortFields[prop] == 'none') {
                this.sortFields[prop] = 'asc';
            } else if (this.sortFields[prop] == 'asc') {
                this.sortFields[prop] = 'desc';
            } else if (this.sortFields[prop] == 'desc') {
                this.sortFields[prop] = 'none';
            }

            this.doRequest();
        },

        doRequest: function() {
            this.$http.post('/api/school/teacher-activities', {
                page: this.page,
                max: this.max,
                sortingRules: this.sortFields,
                filters: this.searchFields
            }, function(response) {
                this.activities = response.activities;
                this.total = response.totalCount;
            });
        }
    }
}
</script>