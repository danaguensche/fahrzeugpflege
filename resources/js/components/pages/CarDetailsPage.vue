<template>
    <MainSidebar></MainSidebar>
    <Page></Page>

</template>
<script>


import Page from '../Cars/CarDetails/Page.vue';
import MainSidebar from '../sidebar/MainSidebar.vue';


export default {
    name: "CalendarPage",
    components: {
        MainSidebar,
        Page

    },

}
</script>
<style scoped></style>
