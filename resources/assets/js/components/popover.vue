<template>
    <div class="popover" v-show="show">
        <div class="arrow-box">
            <slot></slot>
        </div>
    </div>
</template>

<script>
export default {
    props: ['show'],

    data: function() {
        return {

        };
    }
}
</script>