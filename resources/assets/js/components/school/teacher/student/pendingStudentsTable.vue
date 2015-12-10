<template>
    <div class="panel-1">
        <div class="data-table-1">
            <div class="header">
                <div class="title">
                    New students
                </div>
                <div class="filter" v-if="newStudentsOpen">
                    <label>
                        <span>Filter:</span>
                        <input type="text" v-model="mainFilter" placeholder="Type to filter..." />
                    </label>
                </div>
                <div
                        class="collapse-table-button"
                        :class="[newStudentsOpen ? 'open' : 'closed']"
                        @click="newStudentsOpen = !newStudentsOpen"
                ></div>
            </div>
            <div class="table" v-if="newStudentsOpen">
                <table cellspacing="0">
                    <tr>
                        <th>First name</th>
                        <th>Last name</th>
                        <th>Email</th>
                        <th>Activity</th>
                        <th>Status</th>
                    </tr>
                    <tr v-for="student in newStudents | filterBy mainFilter">
                        <td>{{ student.firstName == '' ? 'n/a' : student.firstName }}</td>
                        <td>{{ student.lastName == '' ? 'n/a' : student.lastName }}</td>
                        <td>{{ student.email }}</td>
                        <td>{{ student.activityName }}</td>
                        <td :class="[student.status]"><span>{{ student.status }}</span></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data: function() {
        return {
            newStudents: {},
            mainFilter: '',
            newStudentsOpen: true
        };
    },

    created: function() {
        this.$http.get('/api/school/teacher-new-students', function(data) {
            this.newStudents = data;
        });
    }
};
</script>

<style lang="sass">
    .expired span,
    .pending span {
        padding: 3px;
        color: white;
        -webkit-border-radius: 3px;
        -moz-border-radius: 3px;
        border-radius: 3px;
        font-size: 12px;
        font-weight: 600;
    }

    .expired span {
        background-color: #ac2925;
    }

    .pending span {
        background-color: #f2c200;
    }
</style>