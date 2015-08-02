var Vue = require('vue');

Vue.use(require('vue-resource'));

Vue.http.headers.common['CSRF-TOKEN'] = $('#csrf-token').attr('content');

function buildPhpDateTime(value) {
    var year = value.getFullYear();
    var month = value.getMonth() + 1;
    var day = value.getDate();
    var hour = value.getHours();
    var minute = value.getMinutes();
    var second = value.getSeconds();

    return year + '-' + month + '-' + day + ' ' + hour + ':' + minute + ':' + second;
}