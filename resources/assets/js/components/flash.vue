<template>
    <div v-bind:class="flashClasses" v-show="show">
        <div class="flash-inner-container">
            <p class="flash-message">{{ message }}</p>
            <i class="flash-close fa fa-close" @click="close()"></i>
        </div>
    </div>
</template>

<script>
    export default {
        data: function() {
            return {
                show: false,
                message: '',
                type: 'success'
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
        },

        methods: {
            flash: function(type, message, freeze = false) {
                this.type = type;
                this.message = message;
                this.show = true;

                if (freeze) {
                    return;
                }

                switch (type)
                {
                    case 'success':
                        setTimeout(this.close, 5000);
                        break;
                }
            },

            close: function() {
                this.show = false;
            }
        }
    }
</script>

<style lang="sass">
    .flash {
        position: absolute;
        top: 5px;
        left: 50%;
        width: 60%;
        transform: translateX(-50%);
        border: 1px solid #cccccc;
        -webkit-border-radius: 4px;
        -moz-border-radius: 4px;
        border-radius: 4px;

        .flash-inner-container {
            position: relative;
            padding: 10px 20px;

            .flash-close {
                right: 5px;
                top: 5px;
                position: absolute;
                color: #444;
                cursor: pointer;
                font-size: 14px;
                opacity: 0.4;
            }

            .flash-close:hover {
                opacity: 0.8;
            }

            .flash-message {
                font-size: 17px;
                width: 95%;
                overflow: hidden;
            }
        }
    }

    .flash-success {
        background-color: #dff0d8;
        border-color: #d6e9c6;

        .flash-message {
            color: #3c763d;
        }
    }

    .flash-error {
        background-color: #f2dede;
        border-color: #ebccd1;

        .flash-message {
            color: #a94442;
        }
    }

    @media screen and (max-width: 1000px) {
        .flash {
            width: 80%;
            transform: none;
            left: auto;
            right: 2%;
        }
    }

    @media screen and (max-width: 480px) {
        .flash {
            left: 50%;
            right: auto;
            transform: translateX(-50%);
            width: 90%;

            .flash-message {
                text-align: center;
            }
        }
    }
</style>