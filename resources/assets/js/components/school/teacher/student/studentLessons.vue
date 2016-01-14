<template>
    <div class="lessons">
        <tabs
                :tabs="tabs"
                selected="upcoming"
        >
            <div slot="upcoming">
                <div class="date-group" v-for="lessons in groupedLessons">
                    <div class="date">{{ $key | formatDate }}</div>
                    <div class="lesson" v-for="lesson in lessons | sortBy 'startTime'">
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
                recent
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
            groupedLessons: this.fetchLessons(),
            tabs: [
                {name: 'upcoming', display: 'Upcoming'},
                {name: 'recent', display: 'Recent'},
                {name: 'all', display: 'All'},
            ]
        };
    },

    filters: {
        'formatDate': function(string) {
            return this.moment(string).format('dddd MMMM Do');
        }
    },

    methods: {
        moment: function(str) {
            return this.$parent.getMoment(str);
        },

        fetchLessons: function() {
            this.$http.post('/api/school/teacher/students/' + this.studentId + '/lessons/upcoming', {
                to: this.moment('').add(1, 'months').format('YYYY-MM-DD')
            }, function(response) {
                this.groupedLessons = response.lessons;
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