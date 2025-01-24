<template>
    <div class="profil-page" :class="{ 'profile-page-sidebar-opened': isSidebarOpen }">
        <Settings></Settings> 
        <Form></Form>

    </div>
</template>

<script>

import Form from './Form.vue';
import Settings from './Settings.vue';
import { mapState } from 'vuex';

export default {
    name: "ProfilPage",

    components: {
        Form,
        Settings
    },

    computed: {
        ...mapState(['isSidebarOpen'])
    },
}

</script>

<style scoped>

.hr {
    border: none;
    height: 1px;
    background-color: black;
    margin: 20px 0; 
}
</style>