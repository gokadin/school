module.exports = {
    template: '#payment-student-template',

    props: ['isChecked'],

    methods: {
        markPaid: function() {
            alert('hello');
        }
    },

    components: {
        popover: require('./popover')
    }
};