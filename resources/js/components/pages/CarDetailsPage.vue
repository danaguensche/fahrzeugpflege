<template>
    <MainSidebar></MainSidebar>
    <Details></Details>
</template>
<script>


import Details from '../Cars/CarDetails/Details.vue';
import MainSidebar from '../sidebar/MainSidebar.vue';


export default {
    name: "CalendarPage",
    components: {
        MainSidebar,
        Details,

    },

}

</script>

<style scoped>

</style>
