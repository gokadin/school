<template>
    <div class="panel-1">
        <div class="data-table-1">
            <div class="header">
                <div class="title">
                    Student list
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
                        <th>First name</th>
                        <th>Last name</th>
                        <th>Email</th>
                        <th>Activity</th>
                        <th>Actions</th>
                    </tr>
                    <tr v-for="student in students | filterBy mainFilter">
                        <td>{{ student.firstName }}</td>
                        <td>{{ student.lastName }}</td>
                        <td>{{ student.email }}</td>
                        <td>{{ student.activityName }}</td>
                        <td class="actions">
                            <ul>
                                <li><i class="delete" title="delete" @click="doDelete(student)"></i></li>
                            </ul>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="text" placeholder="Search first name" v-model="oldSearchData['firstName']" @input="inputChanged('firstName')"/>
                        </td>
                        <td>
                            <input type="text" placeholder="Search last name" v-model="oldSearchData['lastName']" @input="inputChanged('lastName')"/>
                        </td>
                        <td>
                            <input type="text" placeholder="Search email" v-model="oldSearchData['email']" @input="inputChanged('email')"/>
                        </td>
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

    <confirm-modal v-ref:confirm>
        <p slot="title">Delete student</p>
        <p slot="message">Are you sure?</p>
    </confirm-modal>
</template>

<script>
    export default {
        data: function() {
            return {
                students: [],
                total: 0,
                page: 0,
                max: 10,
                mainFilter: '',
                oldSearchData: {},
                searchData: {},
                searchDelayTimer: null,
                currentActivity: null,
                updatedActivityData: {}
            };
        },

        computed: {
            hasPreviousPage: function() {
                return this.page > 0;
            },

            hasNextPage: function() {
                return (this.page + 1) * this.max < this.total;
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

            doDelete: function(student) {
                this.$refs.confirm.ifConfirm(function() {
                    this.$http.delete('/api/school/teacher/students/' + student.id, function(response, status) {
                        if (status == 200) {
                            this.doRequest();
                            this.$dispatch('flash', 'success', 'Student deleted!');
                        } else {
                            this.$dispatch('flash', 'error', 'Could not delete student! Please try again.');
                        }
                    }.bind(this));
                }.bind(this));
            },

            doRequest: function() {
                this.$http.post('/api/school/teacher/students/', {
                    page: this.page,
                    max: this.max,
                    sortingRules: this.sortingRules,
                    searchRules: this.searchData
                }, function(response) {
                    this.students = response.data;
                    this.total = response.pagination.totalCount;
                });
            }
        }
    }
</script>

<style lang="sass">

</style>