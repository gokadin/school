<template>
    <div class="panel-1">
        <div class="data-table-1">
            <div class="header">
                <div class="title">
                    {{ title }}
                </div>
                <div class="filter">
                    <label>
                        <span>Filter:</span>
                        <input type="text" v-model="mainFilter" placeholder="Type to filter..." />
                    </label>
                </div>
            </div>
            <div class="table">
                <table cellspacing="0">
                    <tr>
                        <th v-for="colName in columns">
                            {{ colName }}
                        </th>
                    </tr>
                    <tr v-for="activity in activities | filterBy mainFilter">
                        <td>{{ activity.name }}</td>
                        <td>{{ activity.rate }}</td>
                        <td>{{ activity.period }}</td>
                        <td>0</td>
                        <td>x</td>
                    </tr>
                    <tr>
                        <td><input typ="text" placeholder="Search name" v-model="searchName" /></td>
                        <td><input typ="text" placeholder="Search rate" v-model="searchRate" /></td>
                        <td><input typ="text" placeholder="Search period" v-model="searchPeriod" /></td>
                        <td></td>
                        <td></td>
                    </tr>
                </table>
            </div>
            <div class="footer">
                <div class="showing">
                    Showing {{ page * max + 1 }}
                    to {{ ((page + 1) * max) > total ? total : (page + 1) * max }}
                    of {{ total }} <span>entries</span>
                </div>
                <div class="page-selector">
                    <button v-bind:class="{'disabled': !hasPreviousPage}" @click="previousPage()">
                        <i class="fa fa-arrow-left"></i>
                    </button>
                    <button>
                        {{ page + 1 }}
                    </button>
                    <button v-bind:class="{'disabled': !hasNextPage}" @click="nextPage">
                        <i class="fa fa-arrow-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['title', 'uri', 'columns', 'actions'],

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
            this.$http.post(this.uri, {
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

<style lang="sass">

</style>