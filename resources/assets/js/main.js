var Vue = require('vue');

global.jQuery = require('jquery');
var $ = global.jQuery;
window.$ = $;

Vue.use(require('vue-resource'));
var moment = require('moment');
Vue.use(moment);
Vue.use(require('./plugins/dnd'));

var dr = require('dropzone');

window.Vue = Vue;

Vue.http.headers.common['CSRFTOKEN'] = document.getElementById('csrf-token').getAttribute('content');

Vue.component('flash', require('./components/flash.vue'))
Vue.component('infoBox', require('./components/infoBox.vue'))
Vue.component('modal', require('./components/modal.vue'))
Vue.component('confirmModal', require('./components/confirmModal.vue'))
Vue.component('search', require('./components/search.vue'))
Vue.component('searchSelect', require('./components/searchSelect.vue'))
Vue.component('datepicker', require('./components/datepicker.vue'))
Vue.component('popover', require('./components/popover.vue'))
Vue.component('tagSelect', require('./components/tagSelect.vue'))

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
        monthlyCalendar: require('./components/school/teacher/calendar/monthlyCalendar.vue'),
        customizeRegistrationForm: require('./components/school/teacher/setting/registrationForm.vue'),
        upcomingEvents: require('./components/school/teacher/index/upcomingEvents.vue')
    },

    methods: {
        flash: function(type, message, freeze) {
            this.$refs.flash.flash(type, message, freeze);
        },

        getMoment: function(str = '') {
            if (str == '') {
                return moment();
            }

            return moment(str);
        }
    }
});