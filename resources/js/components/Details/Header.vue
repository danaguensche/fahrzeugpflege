<template>
    <v-card-title class="d-flex
                        align-center
                        pa-4
                        bg-blue-grey-darken-3
                        text-white
                        rounded-t-lg">
        <v-icon size="24" class="mr-2">{{ icon }}</v-icon>
        <span class="text-h5 font-weight-medium">{{ title }}</span>
        <v-spacer></v-spacer>
        <v-btn icon @click="switchEditMode" variant="text" color="white">
            <v-tooltip activator="parent" location="bottom">
                Bearbeiten
            </v-tooltip>
            <v-icon>mdi-pencil</v-icon>
        </v-btn>
    </v-card-title>
</template>

<script>
export default {
    name: 'Header',
    props: {
        title: {
            type: String,
            default: ''
        },

        switchEditMode: {
            type: Function,
            default: () => { }
        },
        icon: {
            type: String,
            default: 'mdi-account'
        }
    },
}
</script>