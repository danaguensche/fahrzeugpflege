<template>
    <MainSidebar></MainSidebar>
    <CarPage></CarPage>
</template>

<script>

import MainSidebar from '../sidebar/MainSidebar.vue';
import CarPage from '../Cars/CarPage.vue';

export default {
    name: "CarsPage",
    components: {
        MainSidebar,
        CarPage
    }
}

</script>
