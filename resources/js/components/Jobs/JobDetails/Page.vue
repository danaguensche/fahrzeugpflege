<template>
    <div class="page" :class="{ 'page-opened': isSidebarOpen }">

        <Details></Details>
    </div>
</template>

<script>
import { mapState } from 'vuex';
import Details from './Details.vue';

export default {
    name: 'JobDetailsPage',
    components: {
        Details,
    },
    computed: {
        ...mapState(['isSidebarOpen'])
    },
    data() {
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