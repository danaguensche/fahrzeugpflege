<template>
    <Customer></Customer>
</template>

<script>

import Customer from '../Customers/Customer.vue';
import Footer from '../CommonSlots/Footer.vue';

export default {
    name: "CustomerPage",
    components: {
        Customer,
        Footer,
    }
}

</script>
