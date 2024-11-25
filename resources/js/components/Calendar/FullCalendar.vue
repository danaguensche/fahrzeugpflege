<template>

    <div>
        <VueCal class="calendar" :class="{ 'calendar-sidebar-opened': isSidebarOpen }" locale="de" :time="false"
            hide-weekends hide-view-selector:time="false" :transitions="false" active-view="week"
            :disable-views="['years']" today-button :selected-date="selectedDate" :events="events">
        </VueCal>



    </div>

</template>

<script>
import VueCal from 'vue-cal';
import 'vue-cal/dist/vuecal.css';

import axios from 'axios';

import { mapState } from "vuex"

export default {
    components: {
        VueCal
    },

    computed: {
        ...mapState(['isSidebarOpen'])
    },

    data() {
        return {
            selectedDate: new Date(new Date().getFullYear(), 11, 31),

            events: [
                {
                    start: new Date(),
                    end: new Date(),
                    // You can also define event dates with Javascript Date objects:
                    // start: new Date(2018, 11 - 1, 16, 10, 30),
                    // end: new Date(2018, 11 - 1, 16, 11, 30),
                    title: '',
                    content: '<i class="icon material-icons"></i>',
                    class: ''
                }
            ]
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