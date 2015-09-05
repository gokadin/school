module.exports = {
    template: '#popover-template',

    props: ['on-mark-paid'],

    data: function() {
        return  {
            isShown: false,
            lastState: false
        }
    },

    ready: function() {
        var that = this;

        $(document).mouseup(function(e) {
            var container = $('.popover-template-options');
            var trigger = $('.popover-template-trigger');

            if (trigger.is(e.target))
            {
                that.isShown = false;
                return;
            }

            if (container.is(e.target) || container.has(e.target).length > 0) {
                return;
            }

            that.isShown = false;
            that.lastState = false;
        });

        $(document).keyup(function(e) {
            if (e.keyCode == 27) {
                that.isShown = false;
                that.lastState = false;
            }
        });
    },

    methods: {
        toggleShown: function() {
            this.isShown = !this.lastState;
            this.lastState = this.isShown;
        }
    }
};
