<template>
    <MainSidebar></MainSidebar>
    <Reporting></Reporting>
</template>

<script>

import MainSidebar from '../sidebar/MainSidebar.vue';
import Reporting from '../Reports/Reporting.vue';

export default {
    name: "ReportsPage",
    components: {
        MainSidebar,
        Reporting
    }
}

</script>
