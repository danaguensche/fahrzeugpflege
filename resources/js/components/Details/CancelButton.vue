<template>
    <v-btn variant="tonal" color="error" prepend-icon="mdi-cancel" @click="cancelEdit" :loading="saveLoading">
        Abbrechen
    </v-btn>
</template>

<script>
export default {
    name: 'CancelButton',
    props: {
        saveLoading: {
            type: Boolean,
            default: false
        },

        cancelEdit: {
            type: Function,
            default: () => {}
        }
    },
}
</script>