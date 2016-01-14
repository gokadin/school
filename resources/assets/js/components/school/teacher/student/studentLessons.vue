<template>
    <div class="lessons">
        <tabs
                :tabs="tabs"
                selected="upcoming"
        >
            <div slot="upcoming">
                <div class="no-data" v-if="groupedUpcomingLessons.length == 0">
                    There are no upcoming lessons
                    <a href="/school/teacher/calendar/"><button type="button" class="button-green">Go to calendar</button></a>
                </div>
                <div class="date-group" v-for="group in groupedUpcomingLessons | orderByDate true" v-else>
                    <div class="date">{{ group.date | formatDate }}</div>
                    <div class="lesson" v-for="lesson in group.lessons">
                        <div class="title">{{ lesson.title }}</div>
                        <div class="attendance">
                            <button
                                    :class="{ missed: true, active: !lesson.attended }"
                                    @click="miss(lesson)"
                            >
                                missed
                            </button>
                            <button
                                    :class="{ attended: true, active: lesson.attended }"
                                    @click="attend(lesson)"
                            >
                                attended
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div slot="recent">
                <div class="no-data" v-if="groupedRecentLessons.length == 0">
                    There are no recent lessons
                    <a href="/school/teacher/calendar/"><button type="button" class="button-green">Go to calendar</button></a>
                </div>
                <div class="date-group" v-for="group in groupedRecentLessons | orderByDate false" v-else>
                    <div class="date">{{ group.date | formatDate }}</div>
                    <div class="lesson" v-for="lesson in group.lessons">
                        <div class="title">{{ lesson.title }}</div>
                        <div class="attendance">
                            <button
                                    :class="{ missed: true, active: !lesson.attended }"
                                    @click="miss(lesson)"
                            >
                                missed
                            </button>
                            <button
                                    :class="{ attended: true, active: lesson.attended }"
                                    @click="attend(lesson)"
                            >
                                attended
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div slot="all">
                all
            </div>
        </tabs>
    </div>
</template>

<script>
export default {
    props: ['studentId'],

    data: function() {
        return {
            groupedUpcomingLessons: [],
            groupedRecentLessons: [],
            tabs: [
                {name: 'upcoming', display: 'Upcoming'},
                {name: 'recent', display: 'Recent'},
                {name: 'all', display: 'All'},
            ]
        };
    },

    ready: function() {
        this.fetchUpcomingLessons();
    },

    created: function() {
        this.fetchRecentLessons();
    },

    filters: {
        'formatDate': function(string) {
            return this.moment(string).format('dddd MMMM Do');
        },

        'orderByDate': function(value, asc) {
            return value.slice().sort(function(a, b) {
                var dateA = this.moment(a.date);
                var dateB = this.moment(b.date);

                if (dateA.isBefore(dateB)) {
                    if (asc) {
                        return -1;
                    } else {
                        return 1;
                    }
                } else if (dateB.isBefore(dateA)) {
                    if (asc) {
                        return 1;
                    } else {
                        return -1;
                    }
                }

                return 0;
            }.bind(this));
        }
    },

    methods: {
        moment: function(str) {
            return this.$parent.getMoment(str);
        },

        fetchUpcomingLessons: function() {
            this.$http.post('/api/school/teacher/students/' + this.studentId + '/lessons/range', {
                from: this.moment('').format('YYYY-MM-DD'),
                to: this.moment('').add(1, 'months').format('YYYY-MM-DD')
            }, function(response) {
                this.$set('groupedUpcomingLessons', response.lessons);
            });
        },

        fetchRecentLessons: function() {
            this.$http.post('/api/school/teacher/students/' + this.studentId + '/lessons/range', {
                from: this.moment('').subtract(1, 'months').format('YYYY-MM-DD'),
                to: this.moment('').format('YYYY-MM-DD')
            }, function(response) {
                this.$set('groupedRecentLessons', response.lessons);
            });
        },

        miss: function(lesson) {
            if (!lesson.attended) {
                return;
            }

            lesson.attended = false;
            this.updateAttendance(lesson);
        },

        attend: function(lesson) {
            if (lesson.attended) {
                return;
            }

            lesson.attended = true;
            this.updateAttendance(lesson);
        },

        updateAttendance: function(lesson) {
            this.$http.put('/api/school/teacher/events/' + lesson.eventId + '/lessons/' + lesson.id + '/attendance', {
                date: lesson.startDate,
                attended: lesson.attended
            });
        }
    }
}
</script>