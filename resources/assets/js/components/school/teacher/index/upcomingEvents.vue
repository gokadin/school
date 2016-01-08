<template>
    <div class="upcoming-events">
        <div class="title">Upcoming events</div>
        <div class="date-group" v-for="date in events">
            <div class="date-title">{{ $key | formatDate }}</div>
            <div class="events">
                <div class="event" v-for="event in date | sortBy 'startTime'">
                    {{ event.isAllDay ? 'all day' : 'at ' + event.startTime }} - {{ event.title }}
                </div>
            </div>
        </div>
        <div class="no-data" v-if="events.length == 0">
            You have no calendar events.
            <a href="/school/teacher/calendar/"><button type="button" class="button-green">Go to calendar</button></a>
        </div>
    </div>
</template>

<script>
export default {
    data: function() {
        return {
            events: this.fetchEvents()
        };
    },

    filters: {
        'formatDate': function(string) {
            return this.getMoment(string).format('dddd MMMM Do');
        }
    },

    methods: {
        getMoment: function(str) {
            return this.$root.getMoment(str);
        },

        fetchEvents: function() {
            this.$http.get('/api/school/teacher/events/upcoming-events', function(response) {
                this.events = response.upcomingEvents;
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