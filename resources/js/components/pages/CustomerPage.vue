<template>
    <MainSidebar></MainSidebar>
    <Customer></Customer>
</template>

<script>

import Customer from '../Customers/Customer.vue';
import MainSidebar from '../sidebar/MainSidebar.vue';

export default {
    name: "CustomerPage",
    components: {
        MainSidebar,
        Customer
    }
}

</script>
