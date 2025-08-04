<template>
    <MainSidebar></MainSidebar>
    <JobDetailsPage></JobDetailsPage>
</template>

<script>

import MainSidebar from '../sidebar/MainSidebar.vue';
import JobDetailsPage from '../Jobs/JobDetails/Page.vue';

export default {
    name: "JobsDetailsPage",
    components: {
        MainSidebar,
        JobDetailsPage,
    }
}

</script>
