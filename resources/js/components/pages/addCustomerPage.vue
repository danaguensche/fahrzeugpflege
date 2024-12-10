<template>
    <MainSidebar></MainSidebar>
    <AddCustomer></AddCustomer>
</template>
<script>


import MainSidebar from '../sidebar/MainSidebar.vue';
import AddCustomer from '../Customers/addCustomer/addCustomer.vue';


export default {
    name: "CalendarPage",
    components: {
        MainSidebar,
        AddCustomer

    },

}

</script>

<style scoped>

</style>
