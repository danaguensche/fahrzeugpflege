<template>
    <div class="sidebar-full">
        <div class="sidebar-container rubik-font">
            <div class="sidebar-buttons-wrapper">
                <MenuItemsOpened />
            </div>
        </div>
    </div>
</template>

<script>
import MenuItemsOpened from "./MenuItemsOpened.vue";

export default {
    name: 'SidebarOpened',

    components: {
        MenuItemsOpened
    }
}

</script>

<style scoped>
@import url(../../../css/sidebar/main-sidebar-opened.css)
</style>