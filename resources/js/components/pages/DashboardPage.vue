<template>
    <MainSidebar></MainSidebar>
    <Dashboard></Dashboard>
</template>

<script>

import MainSidebar from '../sidebar/MainSidebar.vue';
import Dashboard from '../Dashboard/Dashboard.vue';

export default {
    name: "DashboardPage",
    components: {
        MainSidebar,
        Dashboard
    }
}

</script>
