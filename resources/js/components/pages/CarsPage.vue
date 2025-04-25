<template>
    <MainSidebar></MainSidebar>
    <Car></Car>
    <Footer></Footer>
</template>

<script>

import MainSidebar from '../sidebar/MainSidebar.vue';
import Car from '../Cars/Car.vue';
import Footer from '../CommonSlots/Footer.vue';

export default {
    name: "CarsPage",
    components: {
        MainSidebar,
        Car,
        Footer,
    }
}

</script>
