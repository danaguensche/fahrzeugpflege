<template>
    <div class="calender">
        <FullCalendar :options="calendarOptions" />
    </div>
</template>

<script>
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import interactionPlugin from '@fullcalendar/interaction'
import axios from 'axios';

export default {
    components: {
        FullCalendar
    },
    data() {
        return {
            calendarOptions: {
                plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: []
            }
        }
    },
    methods: {
        loadEvents() {
            axios.get('api/events').then(response => {
                this.calendarOptions.events = response.data
            }).catch(error => {
                console.error("Fehler beim Laden des Events: ", error)
            })
        }
    },
    mounted() {
        this.loadEvents()
    }
}
</script>

<style>
@import url('../../../css/calendar/main-calendar.css');
</style>