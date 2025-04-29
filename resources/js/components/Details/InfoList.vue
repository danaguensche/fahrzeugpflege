<template>
    <v-list class="bg-transparent" v-if="!editMode">
        <template v-for="key in infoKeys" :key="key">

            <v-list-item v-if="details.data[key] !== undefined">

                <template v-slot:prepend>
                    <v-icon :icon="getIconForField(key)" color="primary" class="mr-2">
                    </v-icon>
                </template>

                <v-list-item-title class="font-weight-medium">
                    {{ labels[key] || key }}
                </v-list-item-title>

                <v-list-item-subtitle class="mt-1 text-body-1">

                    <template v-if="details.data[key] === null || details.data[key] === ''">
                        <span class="text-grey">
                            Keine Daten vorhanden
                        </span>
                    </template>

                    <template v-else>
                        {{ details.data[key] }}
                    </template>

                </v-list-item-subtitle>
            </v-list-item>
            <v-divider v-if="key !== infoKeys[infoKeys.length - 1]"></v-divider>
        </template>
    </v-list>
</template>

<script>
export default {
    name: 'InfoList',
    props: {
        details: {
            type: Object,
            required: true,
        },
        labels: {
            type: Object,
            default: () => ({}),
        },
        editMode: {
            type: Boolean,
            default: false,
        },

        infoKeys: {
            type: Array,
            default: () => []
        },

        getIconForField: {
            type: Function,
            default: () => (field) => {
                return 'mdi-information-outline';
            },
        },

    },
}
</script>