<template>
    <div class="component">
        <!-- Header with control buttons -->
        <div class="header">
            <!-- Data Update Button -->
            <RefreshButton class="refresh-button" @refresh="loadItems" :loading="isRefreshing"></RefreshButton>
            <div class="spacer"></div>

            <!-- Button group for vehicle operations -->
            <div class="button-group">
                <ConfirmButton class="confirm-button" @click="confirmEditItem" :disabled="!editItemId">
                    Bestätigen
                </ConfirmButton>
                <CancelButton class="cancel-button" @click="cancelEdit">Abbrechen</CancelButton>
                <DeleteButton class="delete-button" :disabled="selectedItems.length === 0"
                    @click="confirmDeleteSelectedItems">
                    Löschen
                </DeleteButton>
            </div>
        </div>

        <!-- Table Container -->
        <div class="table-container">
            <!-- Loading indicator -->
            <div v-if="loading" class="loader-container">
                <v-progress-circular indeterminate></v-progress-circular>
            </div>
            
            <!-- Scrolling Table -->
            <div class="scrollable-table">
                <!-- Vuetify server table with pagination and sorting -->
                <v-data-table-server 
                    :headers="headers" 
                    :items="items" 
                    :itemsLength="totalItems"
                    height="calc(100vh - 400px)" 
                    fixed-header 
                    :loading="loading" 
                    @update:options="onOptionsUpdate"
                    :items-per-page="options.itemsPerPage" 
                    :page="options.page"
                    :sort-by="options.sortBy"
                    :multi-sort="false"
                    :must-sort="false"
                    return-object
                    hide-default-footer>

                    <!-- Template for each row of the table -->
                    <template v-slot:item="{ item }">
                        <tr :class="{ 'edited-row': editItemId === item[itemKey] }">
                            <!-- Checkbox for vehicle selection -->
                            <td class="checkbox fixed-width">
                                <v-checkbox v-model="selectedItems" :value="item[itemKey]"></v-checkbox>
                            </td>
                            
                            <!-- Vehicle Data Fields -->
                            <td v-for="field in fields" :key="field" class="fixed-width">
                                <!-- Edit Mode -->
                                <template v-if="editItemId === item[itemKey]">
                                    <!-- Kennzeichen - link only, not editable -->
                                    <template v-if="field === itemKey">
                                        <a :href="`/${detailsUrlBasePath}/${detailsPage}/${item[field] ? item[field].toString().replace(/\s/g, '+') : ''}`">
                                            {{ editItem[field] || '' }}
                                        </a>
                                    </template>
                                    <!-- Services field - not editable -->
                                    <template v-else-if="field === 'services'">
                                        <span v-for="(service, index) in item[field]" :key="service.id">
                                            {{ service.title }}{{ index < item[field].length - 1 ? ', ' : '' }}
                                        </span>
                                    </template>
                                    <!-- The rest of the fields are editable -->
                                    <template v-else>
                                        <template v-if="getHeader(field).type === 'select'">
                                            <v-select
                                                v-model="editItem[field]"
                                                :items="getHeader(field).options"
                                                item-title="title"
                                                item-value="value"
                                                :rules="getFieldRules(field)"
                                                :error-messages="fieldErrors[field]"
                                                density="compact"
                                            ></v-select>
                                        </template>
                                        <template v-else>
                                            <v-text-field 
                                                v-model="editItem[field]" 
                                                :rules="getFieldRules(field)"
                                                :error-messages="fieldErrors[field]" 
                                                density="compact"
                                                :type="field === 'scheduled_at' ? 'datetime-local' : 'text'">
                                            </v-text-field>
                                        </template>
                                    </template>
                                </template>
                                
                                <!-- View Mode -->
                                <template v-else>
                                    <!-- Kennzeichen as link -->
                                    <a v-if="field === itemKey" :href="`/${detailsUrlBasePath}/${detailsPage}/${item[field] ? item[field].toString().replace(/\s/g, '+') : ''}`">
                                        {{ item[field] || '' }}
                                    </a>
                                    <!-- The rest of the fields as plain text -->
                                <span v-else-if="field === 'services'">
                                    <template v-if="Array.isArray(item[field]) && item[field].length > 0">
                                        <span v-for="(service, index) in item[field]" :key="service.id">
                                            {{ service.title }}{{ index < item[field].length - 1 ? ', ' : '' }}
                                        </span>
                                    </template>
                                    <template v-else>
                                        {{ '' }}
                                    </template>
                                </span>
                                <span v-else-if="field === 'status'">
                                    {{ getStatusTitle(item[field]) }}
                                </span>
                                <span v-else-if="field === 'scheduled_at'">
                                    {{ formatDateTime(item[field]) }}
                                </span>
                                <span v-else>{{ item[field] || '' }}</span>
                                </template>
                            </td>
                            
                            <!-- Delete Button -->
                            <td class="table-icon fixed-width">
                                <v-btn icon class="delete-button" variant="plain" @click="confirmDeleteItem(item[itemKey])">
                                    <v-icon>mdi-delete</v-icon>
                                </v-btn>
                            </td>
                            
                            <!-- Edit/Save button -->
                            <td class="table-icon fixed-width">
                                <v-btn variant="plain" icon
                                    @click="editItemId === item[itemKey] ? saveItem() : editItemDetails(item)">
                                    <v-icon>{{ editItemId === item[itemKey] ? 'mdi-content-save' : 'mdi-pencil' }}</v-icon>
                                </v-btn>
                            </td>
                        </tr>
                    </template>
                </v-data-table-server>

                <!-- Pagination Component -->
                <div class="pagination-container">
                    <Pagination 
                        v-model:page="options.page" 
                        v-model:itemsPerPage="options.itemsPerPage"
                        :total-items="totalItems" 
                        :items-per-page-options="[10, 20, 50, 100]"
                        @update:page="handlePageChange" 
                        @update:itemsPerPage="handleItemsPerPageChange" />
                </div>
            </div>
        </div>

        <!-- Modal confirmation window -->
        <VuetifyAlert 
            v-model="isAlertVisible" 
            maxWidth="500" 
            alertTypeClass="alertTypeConfirmation"
            :alertHeading="alertHeading" 
            :alertParagraph="alertParagraph" 
            :alertOkayButton="alertOkayButton"
            alertCloseButton="Abbrechen" 
            @confirmation="handleConfirmation">
        </VuetifyAlert>
    </div>
</template>

<script>
import RefreshButton from '../CommonSlots/RefreshButton.vue';
import axios from 'axios';
import VuetifyAlert from '../Alerts/VuetifyAlert.vue';
import ConfirmButton from '../CommonSlots/ConfirmButton.vue';
import CancelButton from '../CommonSlots/CancelButton.vue';
import DeleteButton from '../CommonSlots/DeleteButton.vue';
import Pagination from '../CommonSlots/Pagination.vue';

export default {
    name: "DataTable",
    
    props: {
        endpoint: {
            type: String,
            required: true
        },
        headers: {
            type: Array,
            required: true
        },
        fields: {
            type: Array,
            required: true
        },
        itemKey: {
            type: String,
            required: true
        },
        detailsPage: {
            type: String,
            required: true
        },
        detailsUrlBasePath: {
            type: String,
            required: true
        },
        deleteKey: {
            type: String,
            default: 'ids'
        },
        searchString: {
            type: String,
            default: ''
        },
        isSearchActive: {
            type: Boolean,
            default: false
        }
    },
    
    components: {
        ConfirmButton,
        CancelButton,
        DeleteButton,
        RefreshButton,
        VuetifyAlert,
        Pagination
    },
    
    data() {
        return {
            isRefreshing: false,
            items: [],
            selectedItems: [],
            itemToDelete: null,
            isAlertVisible: false,
            loading: false,
            totalItems: 0,
            editItemId: null,
            editItem: {},
            fieldErrors: {},
            options: {
                page: 1,
                itemsPerPage: 20,
                sortBy: [{ key: this.itemKey, order: 'desc' }]
            },
            alertHeading: '',
            alertParagraph: '',
            alertOkayButton: '',
            confirmAction: null,
            searchDebounceTimer: null
        };
    },

    computed: {
        currentPage() {
            return this.options.page;
        },
        totalPages() {
            return Math.ceil(this.totalItems / this.options.itemsPerPage);
        },
        isEditing() {
            return this.editItemId !== null;
        },
        pageItems() {
            return Array.from({ length: this.totalPages }, (_, i) => i + 1);
        }
    },

    watch: {
        searchString: {
            handler(newValue, oldValue) {
                if (newValue !== oldValue) {
                    this.handleSearchChange(newValue);
                }
            },
            immediate: false
        },
        isSearchActive: {
            handler(newValue) {
                if (!newValue && !this.searchString) {
                    this.options.page = 1;
                    this.loadItems();
                }
            }
        }
    },

    mounted() {
        console.log('DataTable.vue mounted');
        this.loadItems();
    },

    methods: {
        handleSearchChange(searchValue) {
            if (this.searchDebounceTimer) {
                clearTimeout(this.searchDebounceTimer);
            }

            if (!searchValue || !searchValue.trim()) {
                this.options.page = 1;
                this.loadItems();
                return;
            }

            this.searchDebounceTimer = setTimeout(() => {
                this.options.page = 1;
                this.searchItems(searchValue);
            }, 300);
        },

        async searchItems(query = this.searchString, page = this.options.page) {
            if (!query || !query.trim()) {
                this.loadItems();
                return;
            }

            this.loading = true;
            console.log(`[DataTable] Search started for ${this.endpoint} with query: ${query}.`);
            try {
                const params = {
                    query: query.trim(),
                    page: page,
                    itemsPerPage: this.options.itemsPerPage,
                    sortBy: this.options.sortBy.length > 0 ? this.options.sortBy[0].key : this.itemKey,
                    sortDesc: this.options.sortBy.length > 0 ? this.options.sortBy[0].order === 'desc' : true
                };

                const response = await axios.get(`/api/${this.endpoint}/search`, { params });
                let items = [];
                let total = 0;

                if (response.data?.items) {
                    items = response.data.items;
                    total = response.data.total || response.data.totalItems || 0;
                } else if (response.data?.data && Array.isArray(response.data.data)) {
                    items = response.data.data;
                    total = response.data.total || response.data.data.length;
                } else if (Array.isArray(response.data)) {
                    items = response.data;
                    total = response.data.length;
                }

                this.items = items;
                this.totalItems = total;
                this.options.page = page;
                console.log(`[DataTable] Search completed for ${this.endpoint}. Found ${this.totalItems} items.`);

            } catch (error) {
                console.error(`[DataTable] Error during search for ${this.endpoint}:`, error);
                if (error.response) {
                    console.error('Response data:', error.response.data);
                    console.error('Response status:', error.response.status);
                    console.error('Response headers:', error.response.headers);
                } else if (error.request) {
                    console.error('Request data:', error.request);
                } else {
                    console.error('Error message:', error.message);
                }
                this.items = [];
                this.totalItems = 0;
                this.$emit('show-error', `Error when searching for ${this.endpoint}`);
            } finally {
                this.loading = false;
                console.log(`[DataTable] Search finished for ${this.endpoint}.`);
            }
        },

        onOptionsUpdate(newOptions) {
            let needsReload = false;

            if (newOptions.page !== this.options.page) {
                this.options.page = newOptions.page;
                needsReload = true;
            }

            if (newOptions.itemsPerPage !== this.options.itemsPerPage) {
                this.options.itemsPerPage = newOptions.itemsPerPage;
                this.options.page = 1;
                needsReload = true;
            }

            if (newOptions.sortBy && JSON.stringify(newOptions.sortBy) !== JSON.stringify(this.options.sortBy)) {
                this.options.sortBy = newOptions.sortBy;
                this.options.page = 1;
                needsReload = true;
            }

            if (needsReload) {
                if (this.isSearchActive && this.searchString && this.searchString.trim()) {
                    this.searchItems(this.searchString, this.options.page);
                } else {
                    this.loadItems();
                }
            }
        },

        handlePageChange(page) {
            this.options.page = page;
            if (this.isSearchActive && this.searchString && this.searchString.trim()) {
                this.searchItems(this.searchString, page);
            } else {
                this.loadItems();
            }
        },

        handleItemsPerPageChange(itemsPerPage) {
            this.options.itemsPerPage = itemsPerPage;
            this.options.page = 1;
            if (this.isSearchActive && this.searchString && this.searchString.trim()) {
                this.searchItems(this.searchString, 1);
            }
            else {
                this.loadItems();
            }
        },

        getFieldRules(field) {
            return [value => !!value || `${field} is required`];
        },

        getHeader(field) {
            return this.headers.find(header => header.key === field);
        },

        showAlert(heading, paragraph, okayButton, action) {
            this.alertHeading = heading;
            this.alertParagraph = paragraph;
            this.alertOkayButton = okayButton;
            this.confirmAction = action;
            this.isAlertVisible = true;
        },

        handleConfirmation() {
            if (this.confirmAction) {
                this.confirmAction();
            }
            this.isAlertVisible = false;
        },

        confirmDeleteItem(id) {
            this.itemToDelete = id;
            this.showAlert(
                'Item löschen',
                'Möchten Sie das ausgewählte Item wirklich löschen?',
                'Löschen',
                this.deleteItem
            );
        },

        confirmDeleteSelectedItems() {
            if (this.selectedItems.length > 0) {
                const message = this.selectedItems.length === 1
                    ? 'Möchten Sie das ausgewählte Item wirklich löschen?'
                    : 'Möchten Sie die ausgewählten Items wirklich löschen?';

                const heading = this.selectedItems.length === 1
                    ? 'Item löschen'
                    : 'Items löschen';

                this.showAlert(
                    heading,
                    message,
                    'Löschen',
                    this.deleteItems
                );
            }
        },

        async deleteItem() {
            if (this.itemToDelete) {
                try {
                    await axios.delete(`/api/${this.endpoint}/${this.itemToDelete}`);
                    const isLastItemOnPage = this.items.length === 1;
                    const isNotFirstPage = this.options.page > 1;

                    if (isLastItemOnPage && isNotFirstPage) {
                        this.options.page -= 1;
                    }

                    if (this.isSearchActive && this.searchString && this.searchString.trim()) {
                        await this.searchItems(this.searchString, this.options.page);
                    } else {
                        await this.loadItems();
                    }

                    this.selectedItems = this.selectedItems.filter(id => id !== this.itemToDelete);
                    this.itemToDelete = null;
                } catch (error) {
                    this.$emit('show-error', `Error when deleting item`);
                }
            }
        },

        async deleteItems() {
            if (this.selectedItems.length > 0) {
                try {
                    await axios.delete(`/api/${this.endpoint}`, {
                        data: { [this.deleteKey]: this.selectedItems }
                    });

                    const itemsBeingDeleted = this.selectedItems.length;
                    const remainingItemsOnPage = this.items.length - itemsBeingDeleted;
                    const isNotFirstPage = this.options.page > 1;

                    if (remainingItemsOnPage <= 0 && isNotFirstPage) {
                        this.options.page -= 1;
                    }

                    if (this.isSearchActive && this.searchString && this.searchString.trim()) {
                        await this.searchItems(this.searchString, this.options.page);
                    } else {
                        await this.loadItems();
                    }

                    this.selectedItems = [];
                    this.$emit('itemsDeleted');
                } catch (error) {
                    this.$emit('show-error', `Error when deleting items`);
                }
            }
        },

        async confirmEditItem() {
            try {
                const payload = {};
                this.fields.forEach(field => {
                    payload[field] = this.editItem[field];
                });

                await axios.put(`/api/${this.endpoint}/${this.editItemId}`, payload);
                this.cancelEdit();

                if (this.isSearchActive && this.searchString && this.searchString.trim()) {
                    await this.searchItems(this.searchString, this.options.page);
                } else {
                    await this.loadItems();
                }
            } catch (error) {
                this.$emit('show-error', `Error when saving item`);
            }
        },

        editItemDetails(item) {
            this.editItemId = item[this.itemKey];
            this.editItem = { ...item };
        },

        async saveItem() {
            try {
                const payload = {};
                this.fields.forEach(field => {
                    payload[field] = this.editItem[field];
                });

                await axios.put(`/api/${this.endpoint}/${this.editItemId}`, payload);
                this.cancelEdit();

                if (this.isSearchActive && this.searchString && this.searchString.trim()) {
                    await this.searchItems(this.searchString, this.options.page);
                } else {
                    await this.loadItems();
                }
            } catch (error) {
                this.$emit('show-error', `Error when saving item`);
            }
        },

        cancelEdit() {
            this.editItemId = null;
            this.editItem = {};
            this.fieldErrors = {};
        },

        async loadItems() {
            this.loading = true;
            console.log(`[DataTable] Loading started for ${this.endpoint}.`);

            try {
                const params = {
                    page: this.options.page,
                    itemsPerPage: this.options.itemsPerPage
                };

                if (this.options.sortBy && this.options.sortBy.length > 0) {
                    params.sortBy = this.options.sortBy[0].key;
                    params.sortDesc = this.options.sortBy[0].order === 'asc';
                } else {
                    params.sortBy = this.itemKey;
                    params.sortDesc = true;
                }

                const response = await axios.get(`/api/${this.endpoint}`, { params });
                this.items = response.data.items || response.data || [];
                this.totalItems = response.data.total || response.data.totalItems || this.items.length;
                console.log(`[DataTable] Data loaded successfully for ${this.endpoint}. Total items: ${this.totalItems}`);
            } catch (error) {
                console.error(`[DataTable] Error during data loading for ${this.endpoint}:`, error);
                if (error.response) {
                    console.error('Response data:', error.response.data);
                    console.error('Response status:', error.response.status);
                    console.error('Response headers:', error.response.headers);
                } else if (error.request) {
                    console.error('Request data:', error.request);
                } else {
                    console.error('Error message:', error.message);
                }
                this.items = [];
                this.totalItems = 0;
                this.$emit('show-error', `Error during data loading for ${this.endpoint}`);
            } finally {
                this.loading = false;
                console.log(`[DataTable] Loading finished for ${this.endpoint}.`);
            }
        },

        getStatusTitle(statusCode) {
            const statusHeader = this.headers.find(header => header.key === 'status');
            if (statusHeader && statusHeader.options) {
                const option = statusHeader.options.find(opt => opt.value === statusCode);
                return option ? option.title : statusCode;
            }
            return statusCode;
        },

        formatDateTime(dateTimeString) {
            if (!dateTimeString) return '';
            const date = new Date(dateTimeString);
            const options = {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            };
            return date.toLocaleString('de-DE', options);
        }
    },

    beforeUnmount() {
        if (this.searchDebounceTimer) {
            clearTimeout(this.searchDebounceTimer);
        }
    }
}
</script>
<style scoped>
.button-group {
    display: flex;
    align-items: center;
    gap: 12px;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
    padding: 8px 0;
    margin-right: 20px;
}

.spacer {
    flex-grow: 1;
}

.table-container {
    position: relative;
    width: 100%;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    max-width: 100%;
    margin-right: 20px;
}

.scrollable-table {
    max-height: 75vh;
    overflow-y: auto;
    background-color: #fff;
    width: 90vw;
    max-width: 100%;
}

.pagination-container {
    background-color: #fff;
    padding: 12px;
    border-top: 1px solid #edf2f7;
}

.loader-container {
    display: flex;
    justify-content: center;
    padding: 20px;
}

.fixed-width {
    width: 150px;
    padding: 12px 16px;
}

.table-icon {
    width: 48px;
    text-align: center;
}

/* Tabellenstil */
:deep(.v-data-table) {
    border-collapse: collapse;
    width: 100%;
    table-layout: fixed;
}

:deep(.v-data-table th) {
    background-color: #f5f7fa;
    color: #2c3e50;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.8rem;
    padding: 16px;
    position: sticky;
    top: 0;
    z-index: 10;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

:deep(.v-data-table td) {
    border-bottom: 1px solid #edf2f7;
    padding: 14px 16px;
    color: #333;
}

:deep(.v-data-table tr:hover) {
    background-color: #f9fafc;
}

/* Button-Styling */
.refresh-button :deep(.v-btn),
.confirm-button :deep(.v-btn),
.cancel-button :deep(.v-btn),
.delete-button :deep(.v-btn) {
    text-transform: none;
    font-weight: 500;
    border-radius: 6px;
    padding: 0 16px;
    height: 40px;
    transition: all 0.2s;
}

.confirm-button :deep(.v-btn) {
    background-color: #4caf50;
    color: white;
}

.confirm-button :deep(.v-btn:hover) {
    background-color: #388e3c;
    box-shadow: 0 2px 8px rgba(76, 175, 80, 0.3);
}

.cancel-button :deep(.v-btn) {
    background-color: #f2f2f2;
    color: #333;
}

.cancel-button :deep(.v-btn:hover) {
    background-color: #e0e0e0;
}

.delete-button :deep(.v-btn) {
    background-color: #f44336;
    color: white;
}

.delete-button :deep(.v-btn:hover) {
    background-color: #d32f2f;
    box-shadow: 0 2px 8px rgba(244, 67, 54, 0.3);
}

.refresh-button :deep(.v-btn) {
    background-color: #2196f3;
    color: white;
}

.refresh-button :deep(.v-btn:hover) {
    background-color: #1976d2;
    box-shadow: 0 2px 8px rgba(33, 150, 243, 0.3);
}

/* Icons in Tabelle */
:deep(.v-btn.v-btn--icon) {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    transition: all 0.2s;
}

:deep(.v-btn.v-btn--icon:hover) {
    background-color: rgba(0, 0, 0, 0.05);
}

/* Icon-Farben */
:deep(.mdi-delete) {
    color: #f44336;
}

:deep(.mdi-pencil) {
    color: #2196f3;
}

:deep(.mdi-content-save) {
    color: #4caf50;
}

:deep(.v-text-field) {
    margin: 0;
    padding: 0;
}

:deep(.v-text-field .v-input__control) {
    min-height: 36px;
}

:deep(.v-checkbox) {
    margin: 0;
    padding: 0;
}

a {
    color: #1976d2;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.2s;
}

a:hover {
    color: #0d47a1;
    text-decoration: underline;
}

/* Loading-Anzeige */
:deep(.v-progress-circular) {
    margin: 16px auto;
    display: block;
}

/* Responsives Design */
@media (max-width: 960px) {
    .button-group {
        flex-wrap: wrap;
    }

    .fixed-width {
        width: auto;
    }
}
</style>
