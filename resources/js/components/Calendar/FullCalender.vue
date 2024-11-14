<template>
    <div :class="{ 'calender-sidebar-opened': isSidebarOpen }">
        <FullCalendar :options="calendarOptions" />
    </div>
</template>

<script>
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import interactionPlugin from '@fullcalendar/interaction'

import deLocale from '@fullcalendar/core/locales/de'

import axios from 'axios';

import { mapState } from "vuex"

export default {
    components: {
        FullCalendar
    },

    computed: {
        ...mapState(['isSidebarOpen'])
    },

    data() {
        return {
            calendarOptions: {
                plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
                initialView: 'dayGridMonth',
                locale: deLocale,
                locales: [deLocale],
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