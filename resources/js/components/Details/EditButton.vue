<template>
    <v-btn variant="tonal" color="secondary" prepend-icon="mdi-file-document-edit" @click="switchEditMode">
        Bearbeiten
    </v-btn>
</template>

<script>
export default {
    name: 'EditButton',
    props: {
        editMode: {
            type: Boolean,
            default: false
        },

        switchEditMode: {
            type: Function,
            required: true
        }
    },

}
</script>