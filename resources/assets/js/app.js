var Vue = require('vue');

Vue.use(require('vue-resource'));

Vue.http.headers.common['CSRF-TOKEN'] = $('#csrf-token').attr('content');

new Vue({
    el: '#content'
});