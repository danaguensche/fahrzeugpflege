<template>
    <div class="page" :class="{ 'page-opened': isSidebarOpen }">
        <ProfileSettings/>
    </div>
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
.page {
    transition: margin-left 0.3s ease;
    font-family: var(--font-family);
    margin-left: 50px;
   
}

.page-opened {
    margin-left: 200px;
    transition: margin-left 0.3s ease;
}
</style>