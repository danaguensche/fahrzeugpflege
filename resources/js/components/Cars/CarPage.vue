<template>
    <div class="car-page" :class="{ 'car-page-sidebar-opened': isSidebarOpen }">
            <Search :context="context" class="searchbar" ></Search>
        <Form></Form>
    </div>
</template>

<script>
import Form from './Form.vue';
import Search from '../CommonSlots/Searchbar.vue';

import { mapState } from 'vuex';

export default {
    name: "CarPage",
    computed: {
        ...mapState(['isSidebarOpen'])
    },

    components: {
        Form,
        Search
    },
    data() {
        return {
            context: "Suchen Sie nach einem Fahrzeug...",
        }
    }
}
</script>

<style scoped>



.car-page {
display: flex;
align-items: flex-start;
justify-content: flex-start;
flex-direction: column;

margin-left: 150px;
margin-right: 50px;


transition: left 0.3s ease;
font-family: var(--font-family);
transition: margin-left 0.3s ease;

}

.car-page-sidebar-opened {
margin-left: 350px;
transition: margin-left 0.3s ease;
}

.page-form {}

@media only screen and (max-width:650px) {
.car-page {
    left: 160px;
    font-size: 12px;
    padding: 0px;

    display: flex;
    justify-content: left;
    align-items: flex-start;
}

.car-page-sidebar-opened {
    left: 260px;
    transition: left 0.3s ease;

    .search-icon {
        visibility: hidden;
    }
}

.car-page.h {
    font-size: 12px;
}

.form-wrapper {
    box-shadow: none;
    padding: 0px;
    margin-top: 50px;
}

.searchbar.forms-field {
    width: 300px;
}

.search-icon {
    left: 79%;

}
}
</style>
