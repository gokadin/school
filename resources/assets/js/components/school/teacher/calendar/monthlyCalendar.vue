<template>
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
                            <div
                                    v-for="event in getEventsForDate(getPositionDate(i, j))"
                                    v-bind:class="['event', getColorClass(event)]"
                            >
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
        <div class="modal-1" id="new-event-modal">
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
                            <datepicker :date="newEvent.startDate."></datepicker>
                        </div>
                        <div class="form-row-double">
                            <label>End date</label>
                            <datepicker :date="newEvent.endDate"></datepicker>
                        </div>
                    </div>

                    <div class="form-row">
                        <label>Color</label>
                        <div class="color-options">
                            <div
                                    :class="['color-' + color, isColorSelected(color) ? 'color-selected' : '']"
                                    v-for="color in eventColors"
                                    @click="selectColor(color)"
                            ></div>
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
            events: this.fetchMonthEvents(this.moment()),
            newEvent: {},
            eventColors: ['teal', 'green']
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
                if (this.events[i].startDate == date.format('YYYY-MM-DD')) {
                    events.push(this.events[i]);
                }
            }

            return events;
        },

        nextMonth: function() {
            var nextDate = this.moment(this.currentDate.add(1, 'months').format('YYYY-MM-DD'));
            this.fetchMonthEvents(nextDate);
            this.currentDate = nextDate;
        },

        previousMonth: function() {
            var previousDate = this.moment(this.currentDate.subtract(1, 'months').format('YYYY-MM-DD'));
            this.fetchMonthEvents(previousDate);
            this.currentDate = previousDate;
        },

        today: function() {
            var todayDate = this.moment();
            this.fetchMonthEvents(todayDate);
            this.currentDate = todayDate;
        },

        getColorClass: function(event) {
            switch (event.color) {
                case 'green':
                    return 'event-green';
            }
        },

        isColorSelected: function(color) {
            return color == this.newEvent.color;
        },

        selectColor: function(color) {
            this.newEvent.color = color;
        },

        showAddEvent: function(i, j) {
            this.newEvent.startDate = this.getPositionDate(i, j).format('YYYY-MM-DD');
            this.newEvent.endDate = this.newEvent.startDate;
            this.newEvent.color = 'teal';

            this.$refs.newEventModal.open();
        },

        closeAddEvent: function () {
            this.$refs.newEventModal.close();
        },

        createEvent: function() {
            this.events.push(this.newEvent);

            this.$http.post('/api/school/teacher/events/', {
                title: this.newEvent.title,
                startDate: this.newEvent.startDate,
                endDate: this.newEvent.endDate,
                color: this.newEvent.color
            }, function(response, status) {
                status == 200
                        ? this.$dispatch('flash', 'success', 'Event created!')
                        : this.$dispatch('flash', 'error', 'Failed to create event. Please try again.');
            });

            this.newEvent = {};

            this.closeAddEvent();
        },

        fetchMonthEvents: function(date) {
            this.$http.post('/api/school/teacher/events/range', {
                from: date.clone().date(0).subtract(1, 'weeks').format('YYYY-MM-DD'),
                to: date.clone().date(date.daysInMonth()).add(1, 'months').add(1, 'weeks').format('YYYY-MM-DD')
            }, function(response, status) {
                if (status != 200) {
                    return;
                }

                this.events = response.events;
            });
        }
    }
}
</script>