<template>
    <div class="datetime-container">
        <input
                type="text"
                @click="handleInputClick()"
                @blur="handleBlur()"
                @keyUp.esc="handleEscape()"
                v-model="model"
                :placeholder="placeholder"
                :name="name"
        />
        <div class="box" v-show="show" @mousedown.prevent>
            <div class="header">
                <div class="left-arrow" @click="previousMonth()"><i class="fa fa-chevron-left"></i></div>
                <div class="date-text">{{ currentDate.format('MMMM') }}</div>
                <div class="right-arrow" @click="nextMonth()"><i class="fa fa-chevron-right"></i></div>
                <div class="left-arrow" @click="previousYear()"><i class="fa fa-chevron-left"></i></div>
                <div class="date-text">{{ currentDate.format('YYYY') }}</div>
                <div class="right-arrow" @click="nextYear()"><i class="fa fa-chevron-right"></i></div>
            </div>
            <div class="body">
                <div class="calendar">
                    <table cellspacing="0">
                        <tr>
                            <th>Sun</th><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th>
                        </tr>
                        <tr v-for="i in numRows">
                            <td v-for="j in numCols" width="14%">
                                <div
                                        v-bind:class="[getPositionDate(i, j).month() != currentDate.month() ? 'faded' : '']"
                                        @click="handleDateClick(i, j)"
                                >
                                    {{ getPositionDate(i, j).date() }}
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['model', 'placeholder', 'name'],

    data: function() {
        return {
            show: false,
            currentDate: this.moment()
        };
    },

    computed: {
        numCols: function() {
            return 7;
        },

        numRows: function() {
            return Math.ceil((this.currentDate.daysInMonth() + this.offset) / 7);
        },

        offset: function() {
            return this.getCurrentDateCopy().date(1).day();
        }
    },

    methods: {
        moment: function(str = '') {
            return this.$root.getMoment(str);
        },

        getCurrentDateCopy: function() {
            var date = this.moment();
            date.year(this.currentDate.year());
            date.month(this.currentDate.month());
            date.date(this.currentDate.date());

            return date;
        },

        getPositionDate: function(i, j) {
            var date = this.getCurrentDateCopy();

            if (i == 0 && j < this.offset) {
                date.subtract(1, 'months');
                date.date(date.daysInMonth() - (this.offset - j));
            } else {
                date.date(i * 7 + j - this.offset + 1);
            }

            return date;
        },

        handleInputClick: function() {
            this.showCalendar();
        },

        handleBlur: function() {
            this.show = false;
        },

        handleEscape: function() {
            this.show = false;
        },

        handleDateClick: function(i, j) {
            this.model = this.getPositionDate(i, j).format('YYYY-MM-DD');

            this.show = false;
        },

        nextMonth: function() {
            this.currentDate = this.moment(this.currentDate.add(1, 'months').format('YYYY-MM-DD'));
        },

        previousMonth: function() {
            this.currentDate = this.moment(this.currentDate.subtract(1, 'months').format('YYYY-MM-DD'));
        },

        nextYear: function() {
            this.currentDate = this.moment(this.currentDate.add(1, 'years').format('YYYY-MM-DD'));
        },

        previousYear: function() {
            this.currentDate = this.moment(this.currentDate.subtract(1, 'years').format('YYYY-MM-DD'));
        },

        showCalendar: function() {
            this.currentDate = this.moment(this.model).isValid()
                    ? this.moment(this.model)
                    : this.moment();

            this.show = true;
        }
    }
}
</script>

<style lang="sass">
.datetime-container {
    position: relative;

    .box {
        position: absolute;
        z-index: 10;
        margin-top: 2px;
        width: 260px;
        background-color: white;
        border: 1px solid #cccccc;

        .header {
            display: flex;
            padding: 3px 10px;
            height: 30px;
            align-content: center;

            .date-text {
                flex-basis: 100%;
                text-align: center;
                font-size: 14px;
                line-height: 25px;
            }

            .left-arrow, .right-arrow {
                flex-basis: 25px;
                text-align: center;

                i {
                    border-radius: 3px;
                    cursor: pointer;
                    height: 25px;
                    line-height: 25px;
                    width: 25px;
                }

                i:hover {
                    background-color: #26a69a;
                    color: #fff;
                }
            }
        }

        .body {
            padding: 3px 10px;

            .calendar {
                table {
                    table-layout: fixed;
                    width: 100%;

                    tr {
                        th {
                            font-weight: 400;
                            text-align: center;
                            padding: 3px;
                        }

                        td {
                            height: 30px;
                            line-height: 30px;

                            div {
                                cursor: pointer;
                                border-radius: 3px;
                                height: 100%;
                                text-align: center;
                                font-size: 14px;
                                font-weight: 400;
                            }

                            div.faded {
                                color: #808080;
                            }

                            div:hover, div.faded:hover {
                                background-color: #26a69a;
                                color: #fff;
                            }
                        }
                    }
                }
            }
        }
    }
}
</style>