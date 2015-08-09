var Vue = require('vue');

Vue.use(require('vue-resource'));

Vue.http.headers.common['CSRF-TOKEN'] = $('#csrf-token').attr('content');

new Vue({
    el: '#content',

    components: {
        schoolTeacherStudentCreate: require('./school/teacher/student/create.js'),
        schoolTeacherPaymentIndex: require('./school/teacher/payment/payment.js')
    },

    filters: {
        formatDateForMessage: require('./filters/formatDateForMessage.js')
    }
});