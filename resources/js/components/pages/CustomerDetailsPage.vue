<template>
    <MainSidebar></MainSidebar>
    <Footer></Footer>
</template>
<script>


import MainSidebar from '../sidebar/MainSidebar.vue';
import Footer from '../CommonSlots/Footer.vue';


export default {
    name: "CalendarPage",
    components: {
        MainSidebar,
        Footer,

    },

}

</script>

<style scoped>

</style>
