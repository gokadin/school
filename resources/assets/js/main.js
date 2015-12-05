var Vue = require('vue')
Vue.use(require('vue-resource'));
Vue.http.headers.common['CSRFTOKEN'] = document.getElementById('csrf-token').getAttribute('content');

Vue.component('flash', require('./components/flash.vue'))
Vue.component('dataTable', require('./components/dataTable/dataTable.vue'))
Vue.component('infoBox', require('./components/infoBox.vue'))

new Vue({
    el: 'body',

    components: {
        schoolTeacherActivityIndex: require('./components/school/teacher/activity/index.vue'),
        schoolTeacherStudentIndex: require('./components/school/teacher/student/index.vue'),
        schoolTeacherStudentCreate: require('./components/school/teacher/student/create.vue'),
        schoolTeacherSettingIndex: require('./components/school/teacher/setting/index.vue')
    }
});