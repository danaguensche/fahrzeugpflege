<template>
    <div class="page" :class="{ 'page-opened': isSidebarOpen }">
        <BreadCrumbs :links="customLinks"></BreadCrumbs>
        <Details></Details>
    </div>
</template>

<script>
import { mapState } from 'vuex';
import Details from './Details.vue';
import BreadCrumbs from '../../CommonSlots/BreadCrumbs.vue';

export default {
    components: {
        Details,
        BreadCrumbs
    },
    computed: {
        ...mapState(['isSidebarOpen'])
    },
    data() {
        return {
            customLinks: [
                { title: 'Dashboard', href: '/dashboard', disabled: false },
                { title: 'Fahrzeuge', href: '/fahrzeuge', disabled: false },
                { title: 'Details', href: '/fahrzeuge/fahrzeugdetails', disabled: true },
            ]
        };
    },
}
</script>

<style scoped>
.page {
    margin-left: 250px;
    transition: margin-left 0.3s ease;
    font-family: var(--font-family);
   
}

.page-opened {
    margin-left: 370px;
    transition: margin-left 0.3s ease;
}
</style>