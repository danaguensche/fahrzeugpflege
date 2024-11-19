<template>
    <div class="calendar":class="{ 'calendar-sidebar-opened': isSidebarOpen }">
        <div >
            <VueCal :time="false" small active-view="year" locale="de" style="height: 750px;" />
        </div>
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