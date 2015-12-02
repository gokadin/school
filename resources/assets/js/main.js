var Vue = require('vue')

Vue.use(require('vue-resource'));

Vue.http.headers.common['CSRFTOKEN'] = document.getElementById('csrf-token').getAttribute('content');

var flash = require('./components/flash.vue')
var dataTable = require('./components/dataTable/dataTable.vue')

new Vue({
    el: 'body',

    components: {
        flash: flash,
        dataTable: dataTable
    }
})