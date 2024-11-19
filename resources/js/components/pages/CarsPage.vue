<template>
    <MainSidebar></MainSidebar>
    <Form></Form>
</template>

<script>

import MainSidebar from '../sidebar/MainSidebar.vue';
import Form from '../Cars/Form.vue';

export default {
    name: "CarsPage",
    components: {
        MainSidebar,
        Form
    }
}

</script>
