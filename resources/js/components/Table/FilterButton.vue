<template>
    <div class="filter-button-container">
        <v-menu offset-y :close-on-content-click="false">
            <template v-slot:activator="{ props }">
                <v-btn v-bind="props" class="filter-button" :color="hasActiveFilters ? 'primary' : 'default'"
                    variant="outlined">
                    <v-icon left>mdi-filter-variant</v-icon>
                    Filter
                    <v-badge v-if="hasActiveFilters" :content="selectedStatuses.length" color="primary"
                        inline></v-badge>
                </v-btn>
            </template>

            <v-card min-width="280" class="filter-card">
                <v-divider></v-divider>

                <v-card-text class="filter-content">
                    <v-checkbox v-for="status in statusOptions" :key="status.value" v-model="selectedStatuses"
                        :value="status.value" :label="status.title" hide-details density="comfortable"
                        @change="onFilterChange">
                    </v-checkbox>
                </v-card-text>

            </v-card>
        </v-menu>
    </div>
</template>

<script>
export default {
    name: "FilterButton",

    props: {
        modelValue: {
            type: Array,
            default: () => []
        }
    },

    emits: ['update:modelValue', 'filter-change'],

    data() {
        return {
            selectedStatuses: [...this.modelValue],
            statusOptions: [
                {
                    value: 'ausstehend',
                    title: 'Ausstehend',
                },
                {
                    value: 'in_bearbeitung',
                    title: 'In Bearbeitung',
                },
                {
                    value: 'abgeschlossen',
                    title: 'Abgeschlossen',
                },
                {
                    value: 'im_rueckblick',
                    title: 'Im Rückblick',
                }
            ]
        };
    },

    computed: {
        hasActiveFilters() {
            return this.selectedStatuses.length > 0 &&
                this.selectedStatuses.length < this.statusOptions.length;
        }
    },

    watch: {
        modelValue(newVal) {
            this.selectedStatuses = [...newVal];
        }
    },

    methods: {
        onFilterChange() {
            // Optional: Emit change event for real-time filtering
            this.$emit('update:modelValue', this.selectedStatuses);
            this.$emit('filter-change', this.selectedStatuses);
        },

        applyFilters() {
            this.$emit('update:modelValue', this.selectedStatuses);
            this.$emit('filter-change', this.selectedStatuses);
        },

        clearFilters() {
            this.selectedStatuses = [];
            this.$emit('update:modelValue', []);
            this.$emit('filter-change', []);
        },

        selectAll() {
            if (this.selectedStatuses.length === this.statusOptions.length) {
                this.selectedStatuses = [];
            } else {
                this.selectedStatuses = this.statusOptions.map(s => s.value);
            }
        }
    }
};
</script>

<style scoped>
.filter-button-container {
    display: inline-block;
}

.filter-button {
    text-transform: none;
    font-weight: 500;
    height: 40px;
}

.filter-card {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.filter-title {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px;
    font-size: 1rem;
    font-weight: 600;
}

.filter-content {
    padding: 8px 16px;
    max-height: 400px;
    overflow-y: auto;
}

.status-label {
    display: flex;
    align-items: center;
}

.filter-actions {
    padding: 8px 16px;
}

:deep(.v-checkbox) {
    margin-bottom: 4px;
}

:deep(.v-badge__badge) {
    margin-left: 8px;
}
</style>