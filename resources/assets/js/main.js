var Vue = require('vue')
Vue.use(require('vue-resource'));
Vue.http.headers.common['CSRFTOKEN'] = document.getElementById('csrf-token').getAttribute('content');

Vue.component('flash', require('./components/flash.vue'))
Vue.component('dataTable', require('./components/dataTable/dataTable.vue'))
Vue.component('infoBox', require('./components/infoBox.vue'))

new Vue({
    el: 'body',

    components: {
        schoolTeacherActivityIndex: require('./components/school/teacher/activitiesIndex.vue'),
        schoolTeacherStudentCreate: require('./components/school/teacher/studentsCreate.vue')
    }
});