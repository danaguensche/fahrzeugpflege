<template>
    <div class="sidebar-container closed">
        <div class="sidebar-buttons-wrapper closed">
            <MenuItemsClosed/>
        </div>
    </div>
</template>

<script>
import MenuItemsClosed from './MenuItemsClosed.vue'

export default {
    name: 'SidebarClosed',

    components: {
        MenuItemsClosed
    }
}

</script>


