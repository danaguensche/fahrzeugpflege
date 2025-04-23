<template>
    <MainSidebar></MainSidebar>
    <Page></Page>
</template>
<script>


import MainSidebar from '../sidebar/MainSidebar.vue';
import Page from '../Customers/CustomerDetails/Page.vue';


export default {
    name: "CalendarPage",
    components: {
        MainSidebar,
        Page

    },

}

</script>

<style scoped>

</style>
