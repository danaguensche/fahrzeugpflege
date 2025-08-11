<template>
    <MainSidebar></MainSidebar>
    <Settings></Settings>
</template>

<script>

import MainSidebar from '../sidebar/MainSidebar.vue';
import Settings from '../Settings/Settings.vue';

export default {
    name: "SettingsPage",
    components: {
        MainSidebar,
        Settings,
    }
}

</script>
