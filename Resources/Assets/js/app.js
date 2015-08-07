var Vue = require('vue');

Vue.use(require('vue-resource'));
Vue.use(require('vue-ui'));

Vue.http.headers.common['CSRF-TOKEN'] = $('#csrf-token').attr('content');

new Vue({
    el: '#content',

    components: {
        schoolTeacherPayment: require('./school/teacher/payment/payment.js')
    },

    filters: {
        formatDateForMessage: require('./filters/formatDateForMessage.js')
    }
});