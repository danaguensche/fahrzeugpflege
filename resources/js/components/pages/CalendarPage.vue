<template>
<div class="calender-page">
    <MainSidebar></MainSidebar>
    <FullCalender></FullCalender>
</div>
</template>

<script>

import FullCalender from '../Calendar/FullCalender.vue';
import MainSidebar from '../sidebar/MainSidebar.vue';

export default {
    name: "CalendarPage",
    components: {
        MainSidebar,
        FullCalender
    }
}

</script>

<style scoped>
@import url("../../../css/calendar/page-calender.css");
</style>
