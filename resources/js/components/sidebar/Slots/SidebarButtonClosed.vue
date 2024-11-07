<template>
    <button class="reduce-button closed">
        <slot></slot>
    </button>
</template>

<script>

export default {
    name: 'SidebarButtonClosed'
}

</script>

<style scoped>
@import url(../../../../css/sidebar/sidebar-button-closed.css)
</style>