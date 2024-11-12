<template>
    <MainSidebar></MainSidebar>
    <FullCalender></FullCalender>
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
