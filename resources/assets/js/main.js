var Vue = require('vue')
Vue.use(require('vue-resource'));
Vue.http.headers.common['CSRFTOKEN'] = document.getElementById('csrf-token').getAttribute('content');

Vue.component('flash', require('./components/flash.vue'))
Vue.component('dataTable', require('./components/dataTable.vue'))
Vue.component('infoBox', require('./components/infoBox.vue'))

new Vue({
    el: 'body',

    components: {
        pendingStudentsTable: require('./components/school/teacher/student/pendingStudentsTable.vue'),
        customizeRegistrationForm: require('./components/school/teacher/setting/registrationForm.vue')
    }
});