<template>
    <div class="pa-4">
        <v-form ref="personalInfoForm">
            <template v-for="key in personalInfoKeys" :key="key">
                <v-row no-gutters class="mb-4">
                    <v-col cols="12">
                        <div class="d-flex align-center mb-1">
                            <v-icon :icon="getIconForField(key)" color="primary" class="mr-2">
                            </v-icon>
                            <span class="font-weight-medium">
                                {{ labels[key] }}
                            </span>
                        </div>
                        <v-text-field v-if="key !== 'Sonstiges'" class="w-50 " v-model="editedData[key]"
                            variant="outlined" density="comfortable" hide-details="auto" :readonly="key === 'id'"
                            :disabled="key === 'id'"
                            :hint="key === 'id' ? `${labels[key]} kann nicht bearbeitet werden` : ''"
                            :persistent-hint="key === 'id'">
                        </v-text-field>
                        <v-textarea v-if="key === 'Sonstiges'" class="w-50 tw-resize-y rounded-md" 
                            v-model="editedData[key]" variant="outlined" density="comfortable" 
                            :no-resize="false"></v-textarea>
                    </v-col>

                </v-row>
            </template>
        </v-form>
    </div>
</template>

<script>
export default {
    name: 'InfoListEditMode',
    props: {
        personalInfoKeys: {
            type: Array,
            required: true,
        },
        labels: {
            type: Object,
            required: true,
        },
        editedData: {
            type: Object,
            required: true,
        },

        var: {
            type: String,
            default: 'ID',
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