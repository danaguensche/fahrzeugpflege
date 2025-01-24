<template>
    <v-btn icon>
        <v-icon class="button">mdi-plus</v-icon>
        <div class="text-content">
            <slot></slot>
        </div>
    </v-btn>
</template>

<script>
export default {
    name: "AddButton",
    props: {
        tooltipText: {
            type: String,
            default: null
        }
    },
    computed: {
        showTooltip() {
            return this.tooltipText !== null && this.tooltipText !== '';
        }
    }
}
</script>

<style scoped>

</style>

<style>
@import url("../../../css/tooltips.css");
</style>