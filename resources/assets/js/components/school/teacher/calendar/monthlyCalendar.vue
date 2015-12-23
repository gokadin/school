<template>
    <div id="monthly-calendar">
        <div class="header">
            <div class="navigation">
                <button type="button"><i class="fa fa-chevron-left"></i></button>
                <button type="button" @click="nextMonth()"><i class="fa fa-chevron-right"></i></button>
            </div>
            <div class="today"><button type="button">today</button></div>
            <div class="display">{{ currentDate.format('MMMM YYYY') }}</div>
            <div class="mode"></div>
        </div>
        <div class="body">
            <table cellspacing="0">
                <tr>
                    <th>Sun</th><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th>
                </tr>
                <tr v-for="i in numCols">
                    <td v-for="j in numRows">
                        <div v-bind:class="['top', getPositionDate(i, j).month() != currentDate.month() ? 'faded' : '']">
                            {{ getPositionDate(i, j).date() }}
                        </div>
                        <div class="events">

                        </div>
                        <div class="add"><i class="fa fa-plus-circle" @click="showAddEvent(i, j)"></i></div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <modal v-ref:new-event-modal>
        <div class="modal-1">
            <div class="header">New event</div>
            <div class="body">
                <form class="form-1">
                    <div class="form-row">
                        <label for="startDate">Start date</label>
                        <input id="startDate" type="text" v-model="newEvent.startDate" />
                    </div>
                </form>
            </div>
            <div class="footer">
                <button type="button" class="button-red" @click="closeAddEvent()">Cancel</button>
            </div>
        </div>
    </modal>
</template>

<script>
export default {
    data: function() {
        return {
            currentDate: this.moment(),
            events: [
                {
                    startDate: this.moment(),
                    endDate: this.moment(),
                    title: 'Some event',
                    color: 'blue'
                },
                {
                    startDate: this.moment(),
                    endDate: this.moment(),
                    title: 'Some event',
                    color: 'blue'
                }
            ],
            newEvent: {}
        };
    },

    computed: {
        numCols: function() {
            return Math.ceil(this.currentDate.daysInMonth() / 7);
        },

        numRows: function() {
            return 7;
        },

        offset: function() {
            return this.moment().date(1).day() + 1;
        }
    },

    methods: {
        moment: function() {
            return this.$parent.getMoment();
        },

        getPositionDate: function(i, j) {
            var date = this.moment();

            if (i == 0 && j < this.offset) {
                date.month(date.month() - 1);
                date.date(date.daysInMonth() - (this.offset - j));
            } else {
                date.date(i * 7 + j - this.offset);
            }

            return date;
        },

        showAddEvent: function(rowNum, colNum) {
            this.newEvent.startDate = this.moment();

            this.$refs.newEventModal.open();
        },

        closeAddEvent: function () {
            this.$refs.newEventModal.close();
        },

        nextMonth: function() {
            this.currentDate.month(this.currentDate.month() + 1);
        }
    }
}
</script>