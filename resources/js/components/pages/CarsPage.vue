<template>
    <MainSidebar></MainSidebar>
    <Car></Car>
</template>

<script>

import MainSidebar from '../sidebar/MainSidebar.vue';
import Car from '../Cars/Car.vue';

export default {
    name: "CarsPage",
    components: {
        MainSidebar,
        Car
    }
}

</script>
