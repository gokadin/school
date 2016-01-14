<template>
    <div class="upcoming-events">
        <div class="title">Upcoming events</div>
        <div class="no-data" v-if="groupedEvents.length == 0 && loaded">
            You have no calendar events.
            <a href="/school/teacher/calendar/"><button type="button" class="button-green">Go to calendar</button></a>
        </div>
        <div class="date-group" v-for="group in groupedEvents | orderByDate true" v-else>
            <div class="date-title">{{ group.date | formatDate }}</div>
            <div class="events">
                <div class="event" v-for="event in group.events">
                    {{ event.isAllDay ? 'all day' : 'at ' + event.startTime }} - {{ event.title }}
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data: function() {
        return {
            loaded: false,
            groupedEvents: []
        };
    },

    ready: function() {
        this.fetchEvents();
    },

    filters: {
        'formatDate': function(string) {
            return this.moment(string).format('dddd MMMM Do');
        },

        'orderByDate': function(value, asc) {
            return value.slice().sort(function(a, b) {
                var dateA = this.moment(a.date);
                var dateB = this.moment(b.date);

                if (dateA.isBefore(dateB)) {
                    if (asc) {
                        return -1;
                    } else {
                        return 1;
                    }
                } else if (dateB.isBefore(dateA)) {
                    if (asc) {
                        return 1;
                    } else {
                        return -1;
                    }
                }

                return 0;
            }.bind(this));
        }
    },

    methods: {
        moment: function(str) {
            return this.$root.getMoment(str);
        },

        fetchEvents: function() {
            this.$http.get('/api/school/teacher/events/upcoming-events', function(response) {
                this.$set('groupedEvents', response.events);
                this.loaded = true;
            });
        }
    }
}
</script>

<style lang="sass">
    .upcoming-events {
        .title {
            font-size: 22px;
            margin: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #26a69a;
        }

        .date-group {
            padding: 20px 20px 0 20px;

            .date-title {
                font-size: 18px;
            }

            .events {
                padding: 15px 0;

                .event:not(:last-child) {
                    margin-bottom: 10px;
                }
            }
        }
    }
</style>