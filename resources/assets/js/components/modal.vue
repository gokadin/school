<template>
    <div class="modal-container" v-if="show" transition="modal">
        <div class="modal-veil" @click="closeWithClick">
            <div class="modal-content">
                <slot></slot>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data: function() {
        return {
            show: false
        };
    },

    ready: function() {
        window.addEventListener('keyup', this.handleKeyUp);
    },

    methods: {
        open: function() {
            document.body.style.overflow = 'hidden';
            this.show = true;
        },

        close: function() {
            document.body.style.overflow = 'auto';
            this.show = false;
        },

        closeWithClick: function(e) {
            if ((e.target.className).indexOf('modal-veil') == -1) {
                return;
            }

            this.close();
        },

        handleKeyUp: function(e) {
            if (this.show && e.keyCode == 27) {
                this.close();
            }
        }
    }
}
</script>

<style lang="sass">
    .modal-container {
        .modal-veil {
            z-index: 1050;
            position: fixed;
            width: 100%;
            height: 100%;
            max-height: 100%;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            transition: all .4s ease;

            .modal-content {
                -webkit-border-radius: 3px;
                -moz-border-radius: 3px;
                border-radius: 3px;
                display: table;
                margin: 30px auto;
                z-index: 1150;
                border: 1px solid rgba(0, 0, 0, .33);
                -webkit-box-shadow: 0 2px 8px rgba(0, 0, 0, .33);
                -moz-box-shadow: 0 2px 8px rgba(0, 0, 0, .33);
                box-shadow: 0 2px 8px rgba(0, 0, 0, .33);
                background-color: white;
            }
        }
    }


    .modal-enter .modal-veil, .modal-leave .modal-veil {
        background-color: rgba(0, 0, 0, 0.0);
    }

    .modal-enter ~ .modal-box,
    .modal-leave ~ .modal-box {

    }
</style>