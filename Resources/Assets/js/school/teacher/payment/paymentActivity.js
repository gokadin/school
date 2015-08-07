module.exports = {
    template: '#payment-activity-template',

    data: function() {
        return {
            isOpen: true
        }
    },

    components: {
        paymentStudent: require('./paymentStudent.js')
    }
};