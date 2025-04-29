<template>
    <v-btn variant="elevated" color="success" prepend-icon="mdi-content-save" class="ml-2" @click="saveCarData"
        :loading="saveLoading">
        Speichern
    </v-btn>
</template>

<script>
export default {
    name: 'SaveButton',
    props: {
        saveLoading: {
            type: Boolean,
            default: false
        },
        saveCarData: {
            type: Function,
            default: () => {}
        }
    },
}
</script>