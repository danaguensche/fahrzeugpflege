<template>
    <MainSidebar></MainSidebar>
    <InfoFinder></InfoFinder>
    <ReportForm></ReportForm>
    <!-- With the call to ReportForm, we will specify all the different inputs the form should have and what their attributes are -->
</template>

<script>

import MainSidebar from '../sidebar/MainSidebar.vue';
import ReportForm from '../Reports/ReportForm.vue';

export default {
    name: "ReportFormPage",
    components: {
        MainSidebar,
        ReportForm
    }
}

</script>
