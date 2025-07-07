<template>

    <v-sheet>
        <div class="pa-4 bg-primary-lighten-5 rounded-t-lg">
            <span class="text-h6 font-weight-medium">Metadaten</span>
        </div>
        <v-list class="bg-transparent">
            <v-list-item>
                <template v-slot:prepend>
                    <v-icon icon="mdi-calendar-plus" color="primary" class="mr-2"></v-icon>
                </template>
                <v-list-item-title class="font-weight-medium">{{ labels['created_at'] }}</v-list-item-title>
                <v-list-item-subtitle class="mt-1 text-body-1">
                    {{ formattedCreatedAt }}
                </v-list-item-subtitle>
            </v-list-item>
            <v-divider></v-divider>
            <v-list-item>
                <template v-slot:prepend>
                    <v-icon icon="mdi-calendar-refresh" color="primary" class="mr-2"></v-icon>
                </template>
                <v-list-item-title class="font-weight-medium">{{ labels['updated_at'] }}</v-list-item-title>
                <v-list-item-subtitle class="mt-1 text-body-1">
                    {{ formattedUpdatedAt }}
                </v-list-item-subtitle>
            </v-list-item>
        </v-list>
    </v-sheet>
</template>

<script>
export default {
    name: 'MetaData',
    props: {
        labels: {
            type: Object,
            required: true
        },
        formattedCreatedAt: {
            type: String,
            required: false,
            default: ''
        },
        formattedUpdatedAt: {
            type: String,
            required: false,
            default: ''
        }
    },

}
</script>