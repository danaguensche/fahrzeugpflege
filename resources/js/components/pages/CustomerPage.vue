<template>
    <MainSidebar></MainSidebar>
    <Customer></Customer>
</template>

<script>

import Customer from '../Customers/Customer.vue';
import MainSidebar from '../sidebar/MainSidebar.vue';
import Footer from '../CommonSlots/Footer.vue';

export default {
    name: "CustomerPage",
    components: {
        MainSidebar,
        Customer,
        Footer,
    }
}

</script>
