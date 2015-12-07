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
                        <th v-for="column in columns">
                            {{ column.display }}
                        </th>
                        <th>Actions</th>
                    </tr>
                    <tr v-for="data in dataSet | filterBy mainFilter">
                        <td v-for="column in columns">
                            {{ data[column.name] }}
                        </td>
                        <td>x</td>
                    </tr>
                    <tr>
                        <td v-for="column in columns">
                            <input
                                    type="text"
                                    v-if="column.searchable"
                                    placeholder="Search {{ column.display }}..."
                                    v-model="oldSearchData[column.name]"
                                    @input="inputChanged(column.name)"
                            />
                        </td>
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
                    <button :class="{'disabled': !hasPreviousPage}" @click="previousPage()">
                        <i class="fa fa-arrow-left"></i>
                    </button>
                    <button>
                        {{ page + 1 }}
                    </button>
                    <button :class="{'disabled': !hasNextPage}" @click="nextPage">
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
            dataSet: [],
            total: 0,
            page: 0,
            max: 10,
            mainFilter: '',
            oldSearchData: {},
            searchData: {},
            searchDelayTimer: null
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

    created: function() {
        this.doRequest();
    },

    methods: {
        inputChanged: function(name) {
            if (this.oldSearchData[name] == '') {
                if (name in this.searchData) {
                    delete(this.searchData[name]);

                    this.doSearch();
                }

                return;
            }

            if (!(name in this.searchData)) {
                this.searchData[name] = this.oldSearchData[name];

                this.doSearch();
                return;
            }

            if (this.oldSearchData[name] != this.searchData[name]) {
                this.searchData[name] = this.oldSearchData[name];

                this.doSearch();
            }
        },

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

//        sortBy: function(prop) {
//            if (this.sortFields[prop] == 'none') {
//                this.sortFields[prop] = 'asc';
//            } else if (this.sortFields[prop] == 'asc') {
//                this.sortFields[prop] = 'desc';
//            } else if (this.sortFields[prop] == 'desc') {
//                this.sortFields[prop] = 'none';
//            }
//
//            this.doRequest();
//        },

        doRequest: function() {
            this.$http.post(this.uri, {
                page: this.page,
                max: this.max,
                sortingRules: this.sortingRules,
                searchRules: this.searchData
            }, function(response) {
                this.dataSet = response.data;
                this.total = response.pagination.totalCount;
            });
        }
    }
}
</script>

<style lang="sass">

</style>