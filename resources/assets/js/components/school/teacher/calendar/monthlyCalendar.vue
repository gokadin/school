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
                    <td v-for="j in numCols" v-dropzone:event="dropEvent($dropdata, getPositionDate(i, j))">
                        <div v-bind:class="['top', getPositionDate(i, j).month() != currentDate.month() ? 'faded' : '']">
                            {{ getPositionDate(i, j).date() }}
                        </div>
                        <div class="events">
                            <div
                                    v-for="event in getEventsForDate(getPositionDate(i, j)) | orderBy startTime"
                                    v-draggable:event="{id: event.id, startDate: event.startDate}"
                                    class="event-wrapper"
                            >
                                <div v-bind:class="['event', getColorClass(event)]" @click="showEventId = event.id">
                                    {{ event.title }}
                                </div>
                                <div class="event-popover" v-show="showEventId == event.id">
                                    <div class="arrow-box">
                                        <div class="header">
                                            {{ event.title }}
                                            <div class="close" @click="showEventId = 0"></div>
                                        </div>
                                        <div class="body">
                                            <div class="body-row">
                                                <div class="row-left">Description:</div>
                                                <div class="row-right">
                                                    {{ typeof event.description == 'string' && event.description != '' ? event.description : 'n/a' }}
                                                </div>
                                            </div>
                                            <div class="body-row">
                                                <div class="row-left">Activity:</div>
                                                <div class="row-right">{{ findActivity(event.activityId) ? findActivity(event.activityId).name : 'n/a' }}</div>
                                            </div>
                                            <div class="body-row">
                                                <div class="row-left">Students:</div>
                                                <div class="row-right" v-show="event.studentIds.length == 0">none</div>
                                                <div class="row-right" v-show="event.studentIds.length > 0">
                                                    <div class="view-students" @click="showStudentsFor(event)">view</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="footer">
                                            <button type="button" class="button-red button-short" @click="delete(event)">Delete</button>
                                            <button type="button" class="button-green button-short">Edit</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="extra-events" v-if="getEventCountForDate(getPositionDate(i, j)) > 4">
                                + {{ getEventCountForDate(getPositionDate(i, j)) - 3 }} more
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
                <form class="form-1" id="new-event-form">
                    <div class="form-row">
                        <label for="title">Title</label>
                        <input id="title" type="text" v-model="newEvent.title" placeholder="Title" />
                    </div>

                    <div class="form-row">
                        <label for="description">Description</label>
                        <textarea id="description" v-model="newEvent.description" placeholder="Description"></textarea>
                    </div>

                    <div class="form-row date-and-time">
                        <datepicker class="datepicker" :date="newEvent.startDate"></datepicker>
                        <select v-model="newEvent.startTime" v-show="!newEvent.isAllDay">
                            <option v-for="time in timeSelect" value="{{ time }}">{{ time }}</option>
                        </select>
                        <label class="to">to</label>
                        <datepicker class="datepicker" :date="newEvent.endDate"></datepicker>
                        <select v-model="newEvent.endTime" v-show="!newEvent.isAllDay">
                            <option v-for="time in timeSelect" value="{{ time }}">{{ time }}</option>
                        </select>
                    </div>

                    <div class="form-row recurring-row-1" v-show="newEvent.isRecurring">
                        <label for="rRepeat">Repeat</label>
                        <select name="rRepeat" id="rRepeat" v-model="newEvent.rRepeat">
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="weekdays">Weekdays (Monday to Friday)</option>
                            <option value="monthly">Monthly</option>
                            <option value="yearly">Yearly</option>
                        </select>
                    </div>

                    <div class="form-row recurring-row-2" v-show="newEvent.isRecurring">
                        <label>Ends</label>
                        <div class="checkbox-1">
                            <label>
                                <input type="checkbox" v-model="newEvent.rEndsNever" />
                                <i></i>Never
                            </label>
                        </div>
                        <datepicker class="datepicker" :date="newEvent.rEndDate" v-show="!newEvent.rEndsNever"></datepicker>
                    </div>

                    <div class="form-row event-options">
                        <div class="checkbox-1">
                            <label>
                                <input type="checkbox" v-model="newEvent.isAllDay" />
                                <i></i>All day
                            </label>
                        </div>
                        <div class="checkbox-1">
                            <label>
                                <input type="checkbox" v-model="newEvent.isRecurring" />
                                <i></i>Recurring
                            </label>
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

                    <div :class="['more-options-toggler', showMoreOptions ? 'more-options-open' : '']" @click="showMoreOptions = !showMoreOptions">
                        More options<i class="fa"></i>
                    </div>

                    <div class="more-options" v-show="showMoreOptions">

                        <div class="form-row">
                            <label>Activity</label>
                            <search-select name="activityId"
                                           :data="activities"
                                           value="id"
                                           display="name"
                                           :model.sync="newEvent.activityId"
                                           placeholder="Select an activity"
                            >
                            </search-select>
                        </div>

                        <div class="form-row student-row">
                            <label>Students</label>
                            <tag-select name="studentIds"
                                        uri="/api/school/teacher/students/search"
                                        method="post"
                                        searchkey="search"
                                        :model.sync="newEvent.studentIds"
                                        placeholder="Search students"
                                        display="fullName"
                                        value="id"
                            ></tag-select>
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

    <modal v-ref:students-modal>
        <div class="modal-1">
            <div class="header">
                Attending students
            </div>
            <div class="body">
                <div class="simple-list" v-if="currentStudents.length > 0">
                    <ul>
                        <li v-for="student in currentStudents | orderBy firstName">
                            <div class="picture">
                                <img src="/images/defaultProfilePicture.png" width="30" />
                            </div>
                            <div class="name">{{ student.fullName }}</div>
                            <div class="profile-link">
                                <a href="/school/teacher/students/{{ student.id }}">
                                    <button type="button" class="button-green">
                                        Profile
                                        <i class="fa fa-arrow-circle-right"></i>
                                    </button>
                                </a>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="no-activity-students" v-if="currentStudents.length == 0">
                    There are no students for this activity yet.
                </div>
            </div>
            <div class="footer">
                <button class="button-red button-short" @click="closeStudentsModal()">Close</button>
            </div>
        </div>
    </modal>
</template>

<script>
export default {
    data: function() {
        return {
            currentDate: this.moment(),
            countries: ["Mexico", "USA", "Brazil", "Argentina", "Chile"],
            selectedCountry: null,
            events: this.fetchMonthEvents(this.moment()),
            newEvent: {
                id: 0,
                title: '',
                description: '',
                startDate: this.moment().format('YYYY-MM-DD'),
                endDate: this.moment().format('YYYY-MM-DD'),
                startTime: '12:00pm',
                endTime: '1:00pm',
                isAllDay: true,
                color: 'teal',
                location: '',
                visibility: true,
                activityId: 0,
                studentIds: [],
                isRecurring: false,
                rEndDate: this.moment().add(1, 'years').format('YYYY-MM-DD'),
                rEndsNever: true,
                rRepeat: 'weekly',
                rEvery: 'na',
                notifyMeBy: 'email',
                notifyMeBefore: '15mins',
            },
            eventColors: ['teal', 'green', 'orange', 'red', 'blue', 'light-blue', 'purple', 'golden'],
            timeSelect: ['12:00am', '12:30am', '1:00am', '1:30am', '2:00am', '2:30am', '3:00am',
                '3:30am', '4:00am', '4:30am', '5:00am', '5:30am', '6:00am', '6:30am', '7:00am', '7:30am',
                '8:00am', '8:30am', '9:00am', '9:30am', '10:00am', '10:30am', '11:00am', '11:30am',
                '12:00pm', '12:30pm', '1:00pm', '1:30pm', '2:00pm', '2:30pm', '3:00pm',
                '3:30pm', '4:00pm', '4:30pm', '5:00pm', '5:30pm', '6:00pm', '6:30pm', '7:00pm', '7:30pm',
                '8:00pm', '8:30pm', '9:00pm', '9:30pm', '10:00pm', '10:30pm', '11:00pm', '11:30pm', ],
            showEventId: 0,
            showMoreOptions: false,
            activities: [],
            currentStudents: []
        };
    },

    ready: function() {
        window.addEventListener('keyup', this.handleKeyUp);
    },

    created: function() {
        this.activities = this.fetchActivities();
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
        handleKeyUp: function(e) {
            if (e.keyCode == 27 && this.showEventId != 0) {
                this.showEventId = 0;
            }
        },

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
            var formattedDate = date.format('YYYY-MM-DD');
            var count = 0;

            for (var i = 0; i < this.events.length; i++) {
                if (this.events[i].startDate == formattedDate) {
                    events.push(this.events[i]);
                    count++;
                }

                if (count == 5) {
                    events.pop();
                    events.pop();
                    return events;
                }
            }

            return events;
        },

        getEventCountForDate: function(date) {
            var formattedDate = date.format('YYYY-MM-DD')
            var count = 0;
            for (var i = 0; i < this.events.length; i++) {
                if (this.events[i].startDate == formattedDate) {
                    count++;
                }
            }

            return count;
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
                case 'blue':
                case 'light-blue':
                case 'red':
                case 'purple':
                case 'golden':
                case 'orange':
                    return 'event-' + event.color;
                default:
                    return 'event-teal';
            }
        },

        isColorSelected: function(color) {
            return color == this.newEvent.color;
        },

        selectColor: function(color) {
            this.newEvent.color = color;
        },

        showAddEvent: function(i, j) {
            var posDate = this.getPositionDate(i, j);

            this.newEvent.id = 0;
            this.newEvent.title = '';
            this.newEvent.description = '';
            this.newEvent.startDate = posDate.format('YYYY-MM-DD');
            this.newEvent.endDate = posDate.format('YYYY-MM-DD');
            this.newEvent.startTime = '12:00pm';
            this.newEvent.endTime = '1:00pm';
            this.newEvent.isAllDay = true;
            this.newEvent.color = 'teal';
            this.newEvent.location = '';
            this.newEvent.visibility = true;
            this.newEvent.activityId = this.activities.length > 0 ? this.activities[0].id : 0;
            this.newEvent.studentIds = [];
            this.newEvent.isRecurring = false;
            this.newEvent.rEndDate = posDate.add(1, 'years').format('YYYY-MM-DD');
            this.newEvent.rEndsNever = true;
            this.newEvent.rRepeat = 'weekly';
            this.newEvent.rEvery = 'na';
            this.newEvent.notifyMeBy = 'email';
            this.newEvent.notifyMeBefore = '15mins';

            this.showMoreOptions = false;

            this.$refs.newEventModal.open();
        },

        closeAddEvent: function () {
            this.$refs.newEventModal.close();
        },

        dropEvent: function(data, newDate) {
            var formattedDate = newDate.format('YYYY-MM-DD');

            if (formattedDate == data.startDate) {
                return;
            }

            this.$http.put('/api/school/teacher/events/change-date', {
                id: data.id,
                newStartDate: formattedDate
            }, function(response, status) {
                if (status != 200) {
                    this.$dispatch('flash', 'error', 'Failed to update event. Please try again.');
                } else {
                    for (var i = 0; i < this.events.length; i++) {
                        if (this.events[i].id == data.id) {
                            this.events[i].startDate = formattedDate;
                            this.events[i].endDate = response.newEndDate;
                            this.currentDate = this.currentDate.clone();
                            break;
                        }
                    }
                }
            });
        },

        createEvent: function() {
            this.$http.post('/api/school/teacher/events/', {
                title: this.newEvent.title,
                description: this.newEvent.description,
                startDate: this.newEvent.startDate,
                endDate: this.newEvent.endDate,
                startTime: this.newEvent.startTime,
                endTime: this.newEvent.endTime,
                isAllDay: this.newEvent.isAllDay,
                color: this.newEvent.color,
                activityId: this.newEvent.activityId,
                studentIds: this.newEvent.studentIds,
                isRecurring: this.newEvent.isRecurring,
                rRepeat: this.newEvent.rRepeat,
                rEvery: this.newEvent.rEvery,
                rEndDate: this.newEvent.rEndDate,
                rEndsNever: this.newEvent.rEndsNever,
                location: this.newEvent.location,
                visibility: this.newEvent.visibility,
                notifyMeBy: this.newEvent.notifyMeBy,
                notifyMeBefore: this.newEvent.notifyMeBefore
            }, function(response, status) {
                if (status != 200) {
                    this.$dispatch('flash', 'error', 'Failed to create event. Please try again.');
                } else {
                    this.events.push({
                        id: response.eventId,
                        title: this.newEvent.title,
                        description: this.newEvent.description,
                        startDate: this.newEvent.startDate,
                        endDate: this.newEvent.endDate,
                        startTime: this.newEvent.startTime,
                        endTime: this.newEvent.endTime,
                        isAllDay: this.newEvent.isAllDay,
                        color: this.newEvent.color,
                        location: this.newEvent.location,
                        visibility: this.newEvent.visibility,
                        activityId: this.newEvent.activityId,
                        studentIds: this.newEvent.studentIds,
                        isRecurring: this.newEvent.isRecurring,
                        rEndDate: this.newEvent.rEndDate,
                        rEndsNever: this.newEvent.rEndsNever,
                        rRepeat: this.newEvent.rRepeat,
                        rEvery: this.newEvent.rEvery,
                        notifyMeBy: this.newEvent.notifyMeBy,
                        notifyMeBefore: this.newEvent.notifyMeBefore
                    });

                    this.$dispatch('flash', 'success', 'Event created!');
                }
            });

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
        },

        fetchActivities: function() {
            this.$http.get('/api/school/teacher/activities/', function(response, status) {
                this.activities = response.activities;
                this.activities.unshift({id: 0, name: 'none'});
            });
        },

        findActivity: function(id) {
            for (var i = 0; i < this.activities.length; i++) {
                if (this.activities[i].id == id) {
                    return this.activities[i];
                }
            }
        },

        delete: function(event) {
            this.$http.delete('/api/school/teacher/events/' + event.id, function(response, status) {
                this.$dispatch('flash', 'success', 'Event deleted!');
                this.events.$remove(event);
            });
        },

        showStudentsFor: function(event) {
            this.$http.post('/api/school/teacher/students/from-ids', {
                ids: event.studentIds
            }, function(response, status) {
                this.currentStudents = response.students;
                this.$refs.studentsModal.open();
            });
        },

        closeStudentsModal: function() {
            this.$refs.studentsModal.close();
        }
    }
}
</script>