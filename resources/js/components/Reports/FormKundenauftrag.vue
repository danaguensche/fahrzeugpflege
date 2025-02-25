<template>
<form></form>
</template>

<script>
import axios from 'axios';
import { mapState } from 'vuex';

export default {
   computed: {
        ...mapState(['isSidebarOpen'])
    }
}
</script>

<style scoped>

</style>
