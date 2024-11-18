<template>
    <MainSidebar></MainSidebar>
    <FullCalendar></FullCalendar>
</template>
<script>


import MainSidebar from '../sidebar/MainSidebar.vue';
import FullCalendar from '../Calendar/FullCalendar.vue';


export default {
    name: "CalendarPage",
    components: {
        MainSidebar,
        FullCalendar

    },

}

</script>

<style scoped>

</style>
