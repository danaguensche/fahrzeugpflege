<template>
    <MainSidebar></MainSidebar>
    <FullCalendar></FullCalendar>
    <Footer></Footer>
</template>
<script>


import MainSidebar from '../sidebar/MainSidebar.vue';
import FullCalendar from '../Calendar/FullCalendar.vue';
import Footer from '../CommonSlots/Footer.vue';


export default {
    name: "CalendarPage",
    components: {
        MainSidebar,
        FullCalendar,
        Footer,

    },

}

</script>

<style scoped>

</style>
