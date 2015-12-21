<template>
    <div class="panel-1">
        <div class="data-table-1">
            <div class="header">
                <div class="title">
                    Activity list
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
                        <th>Name</th>
                        <th>Rate</th>
                        <th>Period</th>
                        <th>Students</th>
                        <th>Actions</th>
                    </tr>
                    <tr v-for="activity in activities | filterBy mainFilter">
                        <td>{{ activity.name }}</td>
                        <td>{{ activity.rate }}</td>
                        <td>{{ activity.period }}</td>
                        <td class="student-count"><i @click="showStudents(activity)">{{ activity.studentCount }}</i></td>
                        <td class="actions">
                            <ul>
                                <li><i class="delete" title="delete" @click="doDelete(activity)"></i></li>
                                <li><i class="update" title="update" @click="doUpdate(activity)"></i></li>
                            </ul>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="text" placeholder="Search name" v-model="oldSearchData['name']" @input="inputChanged('name')"/>
                        </td>
                        <td>
                            <input type="text" placeholder="Search rate" v-model="oldSearchData['rate']" @input="inputChanged('rate')"/>
                        </td>
                        <td>
                            <input type="text" placeholder="Search period" v-model="oldSearchData['period']" @input="inputChanged('period')"/>
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
        <p slot="title">Delete activity</p>
        <p slot="message">Are you sure?</p>
    </confirm-modal>

    <modal v-ref:update-modal>
        <div class="modal-1">
            <div class="header">
                Edit activity
            </div>
            <div class="body">
                <form class="form-1">
                    <div class="form-row">
                        <label>Name</label>
                        <input type="text" v-bind:value="updatedActivityData.name" placeholder="Name" v-model="updatedActivityData.name" />
                    </div>
                    <div class="form-row">
                        <label>Rate</label>
                        <input type="text" value="{{ updatedActivityData.rate }}" placeholder="Rate" v-model="updatedActivityData.rate" />
                    </div>
                    <div class="form-row">
                        <label>Period</label>
                        <input type="text" value="{{ updatedActivityData.period }}" placeholder="Period" v-model="updatedActivityData.period" />
                    </div>
                </form>
            </div>
            <div class="footer">
                <button class="button-red button-short" @click="closeConfirmUpdate()">Cancel</button>
                <button class="button-green" @click="confirmUpdate()">Update</button>
            </div>
        </div>
    </modal>

    <modal v-ref:students-modal>
        <div class="modal-1">
            <div class="header">
                Students of {{ currentActivity.name }}
            </div>
            <div class="body">
                <div class="simple-list" v-if="currentStudents.length > 0">
                    <div class="student-filter">
                        <input type="text" placeholder="Filter students" v-model="studentFilter" />
                    </div>
                    <ul>
                        <li v-for="student in currentStudents | filterBy studentFilter">
                            <div class="picture">
                                <img src="/images/defaultProfilePicture.png" width="30" />
                            </div>
                            <div class="name">{{ student.fullName }}</div>
                            <div class="profile-link">
                                <a href="/school/teacher/students/{{ student.id }}">
                                    <button type="button" class="button-green">
                                        Profile
                                        <i class="fa fa-arrow-circle-right"></i>
                                    </button>
                                </a>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="no-activity-students" v-if="currentStudents.length == 0">
                    There are no students for this activity yet.
                </div>
            </div>
            <div class="footer">
                <button class="button-red button-short" @click="closeStudentsModal()">Close</button>
            </div>
        </div>
    </modal>
</template>

<script>
export default {
    data: function() {
        return {
            activities: [],
            total: 0,
            page: 0,
            max: 10,
            mainFilter: '',
            studentFilter: '',
            oldSearchData: {},
            searchData: {},
            searchDelayTimer: null,
            currentActivity: null,
            updatedActivityData: {},
            currentStudents: []
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

        doDelete: function(activity) {
            this.$refs.confirm.ifConfirm(function() {
                this.$http.delete('/api/school/teacher/activities/' + activity.id, function(response, status) {
                    if (status == 200) {
                        this.doRequest();
                        this.$dispatch('flash', 'success', 'Activity deleted!');
                    } else {
                        this.$dispatch('flash', 'error', 'Could not delete activity! Please try again.');
                    }
                }.bind(this));
            }.bind(this));
        },

        doUpdate: function(activity) {
            this.currentActivity = activity;
            this.updatedActivityData.name = activity.name;
            this.updatedActivityData.rate = activity.rate;
            this.updatedActivityData.period = activity.period;
            this.$refs.updateModal.open();
        },

        confirmUpdate: function() {
            this.closeConfirmUpdate();

            this.currentActivity.name = this.updatedActivityData.name;
            this.currentActivity.rate = this.updatedActivityData.rate;
            this.currentActivity.period = this.updatedActivityData.period;

            this.$http.put('/api/school/teacher/activities/' + this.currentActivity.id, {
                name: this.currentActivity.name,
                rate: this.currentActivity.rate,
                period: this.currentActivity.period
            }, function(response, status) {
                if (status == 200) {
                    this.$dispatch('flash', 'success', 'Activity updated!');
                } else {
                    this.$dispatch('flash', 'error', 'Could not update activity! Please try again.');
                }
            });
        },

        closeConfirmUpdate: function() {
            this.$refs.updateModal.close();
        },

        showStudents: function(activity) {
            this.$http.get('/api/school/teacher/activities/' + activity.id + '/students', function(response) {
                this.currentStudents = response.students;
            });

            this.currentActivity = activity;

            this.$refs.studentsModal.open();
        },

        closeStudentsModal: function() {
            this.$refs.studentsModal.close();
        },

        doRequest: function() {
            this.$http.post('/api/school/teacher/activities/', {
                page: this.page,
                max: this.max,
                sortingRules: this.sortingRules,
                searchRules: this.searchData
            }, function(response) {
                this.activities = response.data;
                this.total = response.pagination.totalCount;
            });
        }
    }
}
</script>

<style lang="sass">
    .student-count {
        i {
            font-style: normal;
            cursor: pointer;
            display: inline-block;
            width: 25px;
            height: 25px;
            line-height: 25px;
            border-radius: 3px;
            text-align: center;
        }

        i:hover {
            background-color: #26a69a;
            color: #fff;
        }
    }

    .no-activity-students {
        text-align: center;
        margin: 20px 0;
    }

    .simple-list {
        .student-filter {
            height: 40px;

            input {
                height: 40px;
                width: 100%;
                border-radius: 0;
                border: 1px solid #cccccc;
                padding: 10px;
                font-size: 20px;
            }
        }

        ul {
            width: 100%;
            margin: 20px 0;
            list-style: none;

            li {
                display: flex;
                align-items: center;
                height: 40px;
                border-top: 1px solid #f2f2f2;
                width: 100%;

                .picture {
                    flex-basis: 40px;
                    padding: 5px;
                }

                .name {
                    flex-basis: 90%;
                    font-size: 18px;
                    padding: 0 5px;
                }

                .profile-link {
                    flex-basis: 10%;

                    button {
                        position: relative;

                        i {
                            position: absolute;
                            text-align: right;
                            color: white;
                            right: 8px;
                            height: 30px;
                            line-height: 30px;
                            top: 0;
                        }
                    }
                }
            }

            li:last-child {
                border-bottom: 1px solid #f2f2f2;
            }

            li:hover {
                background-color: #f2f2f2;
            }
        }
    }
</style>