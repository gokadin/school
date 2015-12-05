<template>
    <div v-bind:class="flashClasses" v-show="show">
        <slot></slot>
        <span class="flash-close" @click="show = false">x</span>
    </div>
</template>

<style lang="sass">
.flash {
    color: blue;

    span {
        background-color: yellow;
    }
}

.flash-close {
    color: red;
}
</style>

<script>
    export default {
        props: ['type'],

        data: function() {
            return {
                show: false
            }
        },

        computed: {
            flashClasses: function() {
                var type = this.type;

                return {
                    'flash': true,
                    'flash-success': type == 'success',
                    'flash-error': type == 'error'
                };
            }
        }
    }
</script>