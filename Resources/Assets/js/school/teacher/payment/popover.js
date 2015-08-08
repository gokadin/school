module.exports = {
    template: '#popover-template',

    data: function() {
        return  {
            isShown: false
        }
    },

    ready: function() {
        $(document).mouseup(function(e) {alert(this.isShown);
            //var container = $('#popover-template');

            //if (!container.is(e.target) // if the target of the click isn't the container...
            //    && container.has(e.target).length === 0) // ... nor a descendant of the container
            //{
                this.isShown = false;
            //}
        });
    }
};
