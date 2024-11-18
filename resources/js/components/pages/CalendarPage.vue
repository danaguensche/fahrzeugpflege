<template>
<div class="calender-page">
    <MainSidebar></MainSidebar>
        <FullCalender></FullCalender>
</div>
</template>

<script>


import MainSidebar from '../sidebar/MainSidebar.vue';
import FullCalender from '../Calendar/FullCalender.vue';


export default {
    name: "CalendarPage",
    components: {
        MainSidebar,
        FullCalender

    },

}

</script>

<style scoped>

</style>
