var Vue = require('vue')
Vue.use(require('vue-resource'));
Vue.http.headers.common['CSRFTOKEN'] = document.getElementById('csrf-token').getAttribute('content');

Vue.component('flash', require('./components/flash.vue'))
Vue.component('infoBox', require('./components/infoBox.vue'))
Vue.component('modal', require('./components/modal.vue'))
Vue.component('confirmModal', require('./components/confirmModal.vue'))

new Vue({
    el: 'body',

    created: function() {
        this.$on('flash', function(type, message, freeze = false) {
            this.flash(type, message, freeze);
        });
    },

    components: {
        pendingStudentsTable: require('./components/school/teacher/student/pendingStudentsTable.vue'),
        studentList: require('./components/school/teacher/student/studentList.vue'),
        activityList: require('./components/school/teacher/activity/activityList.vue'),
        customizeRegistrationForm: require('./components/school/teacher/setting/registrationForm.vue')
    },

    methods: {
        flash: function(type, message, freeze) {
            this.$refs.flash.flash(type, message, freeze);
        }
    }
});