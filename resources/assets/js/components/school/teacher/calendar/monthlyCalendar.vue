<template>
    {{ events | json }}
    <div id="monthly-calendar">
        <div class="header">
            <div class="navigation">
                <button type="button" @click="previousMonth()"><i class="fa fa-chevron-left"></i></button>
                <button type="button" @click="nextMonth()"><i class="fa fa-chevron-right"></i></button>
            </div>
            <div class="today" @click="today()"><button type="button">today</button></div>
            <div class="display">{{ currentDate.format('MMMM YYYY') }}</div>
            <div class="mode"></div>
        </div>
        <div class="body">
            <table cellspacing="0">
                <tr>
                    <th>Sun</th><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th>
                </tr>
                <tr v-for="i in numRows">
                    <td v-for="j in numCols">
                        <div v-bind:class="['top', getPositionDate(i, j).month() != currentDate.month() ? 'faded' : '']">
                            {{ getPositionDate(i, j).date() }}
                        </div>
                        <div class="events">
                            <div class="event" v-for="event in getEventsForDate(getPositionDate(i, j))">
                                {{ event.title }}
                            </div>
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
                <form class="form-1 form-1-not-inline">
                    <div class="form-row">
                        <label>Title</label>
                        <input type="text" v-model="newEvent.title" placeholder="Title">
                    </div>

                    <div class="form-row">
                        <div class="form-row-double">
                            <label>Start date</label>
                            <datepicker :date="newEvent.startDate.format('YYYY-MM-DD')"></datepicker>
                        </div>
                        <div class="form-row-double">
                            <label>End date</label>
                            <datepicker :date="newEvent.endDate.format('YYYY-MM-DD')"></datepicker>
                        </div>
                    </div>
                </form>
            </div>
            <div class="footer">
                <button type="button" class="button-red" @click="closeAddEvent()">Cancel</button>
                <button type="button" class="button-green" @click="createEvent()">Create</button>
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
            return this.$parent.getMoment(str);
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

        getEventsForDate: function(date) {
            var events = [];

            for (var i = 0; i < this.events.length; i++) {
                if (this.events[i].startDate.isSame(date, 'day')) {
                    events.push(this.events[i]);
                }
            }

            return events;
        },

        nextMonth: function() {
            this.currentDate = this.moment(this.currentDate.add(1, 'months').format('YYYY-MM-DD'));
        },

        previousMonth: function() {
            this.currentDate = this.moment(this.currentDate.subtract(1, 'months').format('YYYY-MM-DD'));
        },

        today: function() {
            this.currentDate = this.moment();
        },

        showAddEvent: function(i, j) {
            this.newEvent.startDate = this.getPositionDate(i, j);
            this.newEvent.endDate = this.newEvent.startDate;

            this.$refs.newEventModal.open();
        },

        closeAddEvent: function () {
            this.$refs.newEventModal.close();
        },

        createEvent: function() {
            this.events.push(this.newEvent);
            this.newEvent = {};

            this.closeAddEvent();
        }
    }
}
</script>