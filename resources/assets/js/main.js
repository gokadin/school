var Vue = require('vue')
Vue.use(require('vue-resource'));
Vue.http.headers.common['CSRFTOKEN'] = document.getElementById('csrf-token').getAttribute('content');

Vue.component('flash', require('./components/flash.vue'))
Vue.component('dataTable', require('./components/dataTable.vue'))
Vue.component('infoBox', require('./components/infoBox.vue'))
Vue.component('modal', require('./components/modal.vue'))

new Vue({
    el: 'body',

    created: function() {
        this.$on('flash', function(type, message, freeze = false) {
            this.flash(type, message, freeze);
        });
    },

    components: {
        pendingStudentsTable: require('./components/school/teacher/student/pendingStudentsTable.vue'),
        customizeRegistrationForm: require('./components/school/teacher/setting/registrationForm.vue')
    },

    methods: {
        flash: function(type, message, freeze) {
            this.$refs.flash.flash(type, message, freeze);
        }
    }
});