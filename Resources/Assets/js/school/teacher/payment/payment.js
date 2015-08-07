module.exports = {
    template: '#payment-template',

    data: function() {
        return {
            activities: activities
        }
    },

    components: {
        paymentActivity: require('./paymentActivity.js')
    }
};