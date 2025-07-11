<template>
    <MainSidebar></MainSidebar>
    <JobDetails></JobDetails>
</template>

<script>

import MainSidebar from '../sidebar/MainSidebar.vue';
import JobDetails from '../Jobs/JobDetails/Details.vue';

export default {
    name: "JobsDetailsPage",
    components: {
        MainSidebar,
        JobDetails,
    }
}

</script>
