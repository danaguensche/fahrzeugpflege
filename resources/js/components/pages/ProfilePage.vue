<template>
    <MainSidebar></MainSidebar>
    <ProfilPage></ProfilPage>
</template>

<script>

import MainSidebar from '../sidebar/MainSidebar.vue';
import ProfilPage from '../Profil/ProfilPage.vue';
import Footer from '../CommonSlots/Footer.vue';

export default {
    name: "ProfilePage",
    components: {
        MainSidebar,
        ProfilPage,
        Footer,
    }
}

</script>
