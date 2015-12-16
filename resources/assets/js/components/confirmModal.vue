<template>
    <modal v-ref:modal>
        <div class="confirm">
            <div class="header">
                <slot name="title"></slot>
            </div>
            <div class="body">
                <slot name="message"></slot>
            </div>
            <div class="footer">
                <button class="button-gray button-short" @click="cancel()">Cancel</button>
                <button class="button-red" @click="confirm()">Confirm</button>
            </div>
        </div>
    </modal>
</template>

<script>
export default {
    data: function() {
        return {
            callBack: false
        };
    },

    methods: {
        ifConfirm: function(callBack) {
            this.callBack = callBack;
            this.$refs.modal.open();
        },

        cancel: function() {
            this.$refs.modal.close();
            this.callBack = false;
        },

        confirm: function() {
            this.$refs.modal.close();

            if (this.callBack) {
                this.callBack();
            }

            this.callBack = false;
        }
    }
}
</script>

<style lang="sass">
    .confirm {
        width: 500px;

        .header {
            height: 50px;
            line-height: 50px;
            font-size: 16px;
            font-weight: 600;
            padding-left: 15px;
            border-bottom: 1px solid #e5e5e5;
        }

        .body {
            padding: 15px;
            font-size: 16px;
        }

        .footer {
            height: 50px;
            line-height: 50px;
            border-top: 1px solid #e5e5e5;
            padding-right: 15px;
            text-align: right;

            button:first-child {
                margin-right: 5px;
            }
        }
    }
</style>