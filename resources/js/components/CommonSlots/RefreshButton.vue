<template>
    <v-btn :disabled="loading" class="bg-blue-grey-darken-2 border-0" append-icon="mdi-refresh" text="Update" variant="outlined" @click="refresh"></v-btn>
</template>

<script>
export default {
    name: "RefreshButton",
    props: {
        loading: {
            type: Boolean,
            required: true
        }
    },
    methods: {
        refresh() {
            this.$emit('refresh');
        }
    }
}
</script>

<style scoped>

.button {
}
</style>
