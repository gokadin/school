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
                        <th v-bind:class="['sortable', sortFields.name == 'asc' ? 'sorted-asc' : '', sortFields.name == 'desc' ? 'sorted-desc' : '']" @click="sortBy('name')">Name</th>
                        <th v-bind:class="['sortable', sortFields.rate == 'asc' ? 'sorted-asc' : '', sortFields.rate == 'desc' ? 'sorted-desc' : '']" @click="sortBy('rate')">Rate</th>
                        <th v-bind:class="['sortable', sortFields.period == 'asc' ? 'sorted-asc' : '', sortFields.period == 'desc' ? 'sorted-desc' : '']" @click="sortBy('period')">Period</th>
                        <th v-bind:class="['sortable', sortFields.students == 'asc' ? 'sorted-asc' : '', sortFields.students == 'desc' ? 'sorted-desc' : '']" @click="sortBy('students')">Students</th>
                        <th>Actions</th>
                    </tr>
                    <tr v-for="activity in activities | filterBy mainFilter">
                        <td>@{{ activity.name }}</td>
                        <td>@{{ activity.rate }}</td>
                        <td>@{{ activity.period }}</td>
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
        </div>
    </div>
</template>

<style lang="sass">

</style>

<script>
export default {
    props: ['title'],

    data: function() {
        return {
            mainFilter: ''
        }
    }
}
</script>