<template>
    <div class="car-page" :class="{ 'car-page-sidebar-opened': isSidebarOpen }">
            <Search :context="context" class="searchbar" ></Search>
        <Form></Form>
    </div>
</template>

<script>
import Form from './Form.vue';
import Search from './Slots/Search.vue';

import { mapState } from 'vuex';

export default {
    name: "Page",
    computed: {
        ...mapState(['isSidebarOpen'])
    },

    components: {
        Form,
        Search
    },
    data() {
        return {
            context: "Suchen Sie nach einem Fahrzeug..."
        }
    }
}
</script>

<style>
@import url("../../../css/cars/cars.css");
</style>
