<template>
    <v-list class="bg-transparent" v-if="!editMode">
        <template v-for="key in infoKeys" :key="key">
            <v-list-item v-if="resolvedDetails[key] !== undefined">
                <template v-slot:prepend>
                    <v-icon :icon="getIconForField(key)" color="primary" class="mr-2">
                    </v-icon>
                </template>
                <v-list-item-title class="font-weight-medium">
                    {{ labels[key] || key }}
                </v-list-item-title>
                <v-list-item-subtitle v-if="key !== 'Beschreibung' && key !== 'Sonstiges'" class="mt-1 text-body-1 white-space-normal">
                    <template v-if="resolvedDetails[key] === null || resolvedDetails[key] === ''">
                        <span class="text-grey">
                            Keine Daten vorhanden
                        </span>
                    </template>
                    <template v-else>
                        {{ resolvedDetails[key] }}
                    </template>
                </v-list-item-subtitle>
                <v-list-item-title v-if="key === 'Beschreibung' || key === 'Sonstiges'" class="mt-1 text-body-1 white-space-normal scrollable">
                    <template v-if="resolvedDetails[key] === null || resolvedDetails[key] === ''">
                        <span class="text-grey">
                            Keine Beschreibung vorhanden
                        </span>
                    </template>
                    <template v-else>
                        {{ resolvedDetails[key] }}
                    </template>
                </v-list-item-title>

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
    computed: {
        resolvedDetails() {
            console.log('Details prop:', this.details);
            // Check if details has a 'data' property, if so, use it. Otherwise, use details directly.
            return this.details && this.details.data ? this.details.data : this.details;
        }
    }
}
</script>
<style scoped>
.white-space-normal {
    white-space: normal !important;
}

.scrollable {
    margin-top: 10px;
    margin-bottom: 10px;
    max-width: 90%;
    max-height: 150px;
    overflow-y: auto;
}
</style>