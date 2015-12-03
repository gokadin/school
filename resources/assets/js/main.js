var Vue = require('vue')
Vue.use(require('vue-resource'));
Vue.http.headers.common['CSRFTOKEN'] = document.getElementById('csrf-token').getAttribute('content');

Vue.component('flash', require('./components/flash.vue'))
Vue.component('dataTable', require('./components/dataTable/dataTable.vue'))

new Vue({
    el: 'body',

    data: {
        currentView: 'activitiesIndex'
    },

    components: {
        activitiesIndex: require('./components/school/teacher/activitiesIndex.vue')
    }
});