<template>
    <v-container>
        <ProfileSettings/>
    </v-container>
    <div class="profil-page" :class="{ 'profile-page-sidebar-opened': isSidebarOpen }">


    </div>
</template>

<script>

import ProfileSettings from './ProfileSettings.vue';


import { mapState } from 'vuex';

export default {
    name: "ProfilPage",

    components: {
        ProfileSettings
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