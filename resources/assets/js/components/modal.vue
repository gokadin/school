<template>
    <div class="modal-veil" v-if="show" @click="close()"></div>
    <div class="modal" v-if="show">
        <slot></slot>
    </div>
</template>

<script>
export default {
    data: function() {
        return {
            show: true
        };
    },

    ready: function() {
        window.addEventListener('keyup', this.handleKeyUp);
    },

    methods: {
        open: function() {
            this.show = true;
        },

        close: function() {
            this.show = false;
        },

        handleKeyUp: function(e) {
            if (e.keyCode == 27) {
                this.close();
            }
        }
    }
}
</script>

<style lang="sass">
    .modal-veil {
        z-index: 195;
        position: fixed;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        opacity: 0.4;
        background-color: black;
    }

    .modal {
        z-index: 199;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translateX(-50%) translateY(-50%);
        border: 1px solid red;
    }
</style>