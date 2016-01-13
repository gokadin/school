<template>
    <div class="lessons">
        <div class="lesson" v-for="lesson in lessons">
            {{ lesson.startDate }}
        </div>
    </div>
</template>

<script>
export default {
    props: ['studentId'],

    data: function() {
        return {
            lessons: this.fetchLessons()
        };
    },

    methods: {
        moment: function(str) {
            return this.$parent.getMoment(str);
        },

        fetchLessons: function() {
            this.$http.post('/api/school/teacher/students/' + this.studentId + '/lessons', {
                from: this.moment('').subtract(2, 'weeks').format('YYYY-MM-DD'),
                to: this.moment('').add(1, 'months').format('YYYY-MM-DD')
            }, function(response) {
                this.lessons = response.lessons;
            });
        }
    }
}
</script>