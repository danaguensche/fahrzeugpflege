<template>
    <button class="expand-button">
        <slot></slot>
    </button>
</template>

<script>
export default {
    name: 'ExpandButton'
}
</script>

<style scoped>
@import url(../../../../css/sidebar/expand-button.css)
</style>