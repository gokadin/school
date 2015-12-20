<template>
    <div class="search">
        <div class="search-wrapper">
            <input type="text" id="search" placeholder="Search..."
                   v-model="search" debounce="300" autocomplete="off" maxlength="50" />
            <i class="fa fa-search"></i>
            <div id="search-results" @mousedown.prevent>
                <div class="no-results" v-show="students.length == 0 && activities.length == 0">
                    no results
                </div>
                <div class="result-section" v-show="students.length > 0">
                    <div class="title">
                        <div>students</div>
                        <div>{{ students.length }}{{ students.length == 10 ? '+' : '' }} {{ students.length > 1 ? 'results' : 'result' }}</div>
                    </div>
                    <div class="row" v-for="student in students">
                        <a href="/school/teacher/students/{{ student.id }}">
                            {{ student.firstName}} {{ student.lastName }}
                        </a>
                    </div>
                </div>
                <dib class="result-section" v-show="activities.length > 0">
                    <div class="title">
                        <div>Activities</div>
                        <div>{{ activities.length }}{{ activities.length == 10 ? '+' : '' }} {{ activities.length > 1 ? 'results' : 'result' }}</div>
                    </div>
                    <div class="row" v-for="activity in activities">
                        <a href="/school/teacher/activities/">
                            {{ activity.name }}
                        </a>
                    </div>
                </dib>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data: function() {
        return {
            students: [],
            activities: []
        }
    },

    ready: function() {
        window.addEventListener('keyup', this.handleKeyUp);
    },

    watch: {
        'search': function(val) {
            val = val.trim();

            if (val == '') {
                this.students = [];
                this.activities = [];
                return;
            }

            this.doRequest(val);
        }
    },

    methods: {
        doRequest: function(search) {
            this.$http.get('/api/school/teacher/search/' + encodeURI(search), function(response) {
                this.students = response.students;
                this.activities = response.activities;
            });
        },

        handleKeyUp: function(e) {
            if (e.keyCode == 27) {
                document.getElementById('search').blur();
            } else if (e.keyCode == 13 && document.getElementById('search') === document.activeElement) {
                if (this.students.length > 0) {
                    location.href = '/school/teacher/students/' + this.students[0].id;
                } else if (this.activities.length > 0) {
                    location.href = '/school/teacher/activities/';
                }
            }
        }
    }
}
</script>

<style lang="sass">
    .search {
        height: 100px;

        .search-wrapper {
            position: relative;
            margin: 0 auto;
            width: 230px;
            height: 100%;

            #search {
                height: 40px;
                width: 230px;
                margin-top: 30px;
                background-color: #263238;
                border: none;
                border-bottom: 1px solid #c7cacb;
                color: #c7cacb;
                padding: 0 20px 0 5px;
            }

            #search:focus {
                color: #fff;
                border-bottom: 1px solid #fff;

                ~ #search-results {
                      display: block;
                  }
            }

            > i {
                position: absolute;
                color: #c7cacb;
                height: 20px;
                top: 50%;
                margin-top: -8px;
                right: 2px;
            }

            #search-results {
                display: none;
                position: absolute;
                width: 230px;
                background-color: white;
                margin-top: 5px;
                left: 0;
                z-index: 100;
                max-height: 400px;
                overflow-y: auto;
                box-shadow: 0px 1px 3px rgba(0, 0, 0, 0.1);

                .no-results {
                    height: 30px;
                    line-height: 30px;
                    text-align: center;
                }

                .result-section {
                    .title {
                        display: flex;
                        height: 22px;
                        background-color: #4C4C4C;
                        padding: 0 5px;
                        color: #fff;
                        align-items: center;

                        > div {
                              flex: 1;
                              color: #fff;
                          }

                        > div:last-child {
                            text-align: right;
                        }
                    }

                    .row {
                        height: 30px;
                        line-height: 30px;
                        border-top: 1px solid #e2e2e2;

                        a {
                            display: block;
                            width: 100%;
                            height: 100%;
                            padding: 0 5px;
                            font-weight: 600;
                            font-size: 14px;
                            color: #808080;
                        }
                    }

                    .row:hover {
                        background-color: #f2f2f2;

                        a { color: #444; }
                    }
                }
            }
        }
    }
</style>