var Vue = require('vue')
Vue.use(require('vue-resource'));
Vue.http.headers.common['CSRFTOKEN'] = document.getElementById('csrf-token').getAttribute('content');

var flash = require('./components/flash.vue')
var dataTable = require('./components/dataTable/dataTable.vue')

var act = require('./components/school/teacher/activitiesIndex.vue')

act = Vue.extend({
    template: '<h1>ffff</h1>'
})

var profile = new act({
    data: {
        uri: 'oowwwwfaefwagagaega',
        firstName: 'Walter',
        lastName: 'White',
        alias: 'Heisenberg'
    }
})
// mount it on an element
profile.$mount('#mount-point')

new Vue({
    el: 'body',

    components: {
        flash: flash,
        dataTable: dataTable,
        activitiesIndex: act
    }
})