<template>
    <MainSidebar></MainSidebar>
    <Jobs></Jobs>
</template>

<script>

import MainSidebar from '../sidebar/MainSidebar.vue';
import Jobs from '../Jobs/Jobs.vue';
import Footer from '../CommonSlots/Footer.vue';

export default {
    name: "JobsPage",
    components: {
        Jobs,
        MainSidebar,
        Footer,
    }
}

</script>
