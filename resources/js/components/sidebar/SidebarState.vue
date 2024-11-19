<template>
    <div class="sidebar container" :class="{ 'sidebar-container closed': !isSidebarOpen }">
        <div class="sidebar-buttons-wrapper" :class="{ 'sidebar-buttons-wrapper closed': !isSidebarOpen }">
            <MenuItemsOpened v-if="isSidebarOpen"></MenuItemsOpened>
            <MenuItemsClosed v-if="!isSidebarOpen"></MenuItemsClosed>
        </div>
    </div>
</template>

<script>
import MenuItemsClosed from './MenuItemsClosed.vue'
import MenuItemsOpened from './MenuItemsOpened.vue';

import { mapState } from "vuex"

export default {
    name: 'SidebarState',

    components: {
        MenuItemsClosed,
        MenuItemsOpened
    },

    computed: {
        ...mapState(['isSidebarOpen'])
    },
}

</script>
