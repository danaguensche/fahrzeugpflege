<template>
    <MainSidebar></MainSidebar>
    <Page></Page>
</template>

<script>

import MainSidebar from '../sidebar/MainSidebar.vue';
import Page from '../Cars/Page.vue';

export default {
    name: "CarsPage",
    components: {
        MainSidebar,
        Page
    }
}

</script>
