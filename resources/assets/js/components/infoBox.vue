<template>
    <div id="info-box" v-if="show">
        <slot></slot>
        <div class="info-box-icon">
            <i class="fa fa-question-circle"></i>
        </div>
        <div class="info-box-close" @click="close()">
            <i class="fa fa-times"></i>
        </div>
    </div>
</template>

<script>
export default {
    data: function() {
        return {
            show: true
        };
    },

    methods: {
        close: function() {
            this.show = false;
        }
    }
}
</script>

<style lang="sass">
#info-box {
    position: relative;
    border: 1px solid #b3b3b3;
    border-radius: 3px;
    background-color: #e9ffe6;
    padding: 10px 100px;
    margin-bottom: 20px;

    p {
        font-size: 17px;
        font-family: Helvetica, Roboto, SansSerif;
        color: black;
    }

    .info-box-icon {
        position: absolute;
        top: 15px;
        left: 15px;
        height: 50px;
        line-height: 50px;
        font-size: 50px;

        i {
            color: #67b168;
        }
    }

    .info-box-close {
        position: absolute;
        -webkit-border-radius: 3px;
        -moz-border-radius: 3px;
        border-radius: 3px;
        height: 25px;
        width: 25px;
        text-align: center;
        line-height: 25px;
        top: 5px;
        right: 5px;
        cursor: pointer;
        font-size: 25px;

        i {
            color: #d24d4d;
        }
    }

    a {
        border: 1px solid #cccccc;
        font-size: 14px;
        display: block;
        width: 180px;
        padding: 5px;
        margin: 10px 0 5px 0;
        background-color: #67b168;
        color: #fff;
        font-weight: 600;
    }
}
</style>