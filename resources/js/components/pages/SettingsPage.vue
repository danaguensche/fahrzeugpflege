<template>
    <MainSidebar></MainSidebar>
    <Footer></Footer>
</template>

<script>

import Footer from '../CommonSlots/Footer.vue';
import MainSidebar from '../sidebar/MainSidebar.vue';

export default {
    name: "SettingsPage",
    components: {
        MainSidebar,
        Footer
    }
}

</script>
