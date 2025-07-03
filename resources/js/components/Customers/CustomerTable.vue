<template>
    <div class="component">
        <!-- Header with control buttons -->
        <div class="header">
            <!-- Data Update Button -->
            <RefreshButton class="refresh-button" @refresh="loadItems"></RefreshButton>
            <div class="spacer"></div>

            <!-- Button group for customer operations -->
            <div class="button-group">
                <ConfirmButton class="confirm-button" @click="confirmEditCustomer" :disabled="!editCustomerId">
                    Bestätigen
                </ConfirmButton>
                <CancelButton class="cancel-button" @click="cancelEdit">Abbrechen</CancelButton>
                <DeleteButton class="delete-button" :disabled="selectedCustomers.length === 0"
                    @click="confirmDeleteSelectedCustomers">
                    Löschen
                </DeleteButton>
            </div>
        </div>

        <!-- Table Container -->
        <div class="table-container">
            <!-- Download indicator -->
            <div v-if="loading" class="loader-container">
                <v-progress-circular indeterminate></v-progress-circular>
            </div>
            
            <!-- Scrolling Table -->
            <div class="scrollable-table">
                <!-- Vuetify server table with pagination and sorting -->
                <v-data-table-server 
                    :headers="headers" 
                    :items="customers" 
                    :itemsLength="totalItems"
                    height="calc(100vh - 400px)" 
                    fixed-header 
                    :loading="loading" 
                    @update:options="onOptionsUpdate"
                    :items-per-page="options.itemsPerPage" 
                    :page="options.page"
                    :sort-by="options.sortBy"
                    :multi-sort="false"
                    hide-default-footer>

                    <!-- Template for each row of the table -->
                    <template v-slot:item="{ item }">
                        <tr :class="{ 'edited-row': editCustomerId === item.id }">
                            <!-- Checkbox for client selection -->
                            <td class="checkbox fixed-width">
                                <v-checkbox v-model="selectedCustomers" :value="item.id"></v-checkbox>
                            </td>
                            
                            <!-- Customer Data Fields -->
                            <td v-for="field in fields" :key="field" class="fixed-width">
                                <!-- Edit Mode -->
                                <template v-if="editCustomerId === item.id">
                                    <!-- ID - link only, not editable -->
                                    <template v-if="field === 'id'">
                                        <a :href="`/kunden/kundendetails/${editCustomer[field] || ''}`">
                                            {{ editCustomer[field] || '' }}
                                        </a>
                                    </template>
                                    <!-- The rest of the fields are editable -->
                                    <template v-else>
                                        <v-text-field 
                                            v-model="editCustomer[field]" 
                                            :rules="getFieldRules(field)"
                                            :error-messages="fieldErrors[field]" 
                                            density="compact">
                                        </v-text-field>
                                    </template>
                                </template>
                                
                                <!-- View Mode -->
                                <template v-else>
                                    <!-- ID as reference -->
                                    <a v-if="field === 'id'" :href="`/kunden/kundendetails/${item[field] || ''}`">
                                        {{ item[field] || '' }}
                                    </a>
                                    <!-- The rest of the fields as plain text -->
                                    <span v-else>{{ item[field] || '' }}</span>
                                </template>
                            </td>
                            
                            <!-- Delete Button -->
                            <td class="table-icon fixed-width">
                                <v-btn icon class="delete-button" variant="plain" @click="confirmDeleteCustomer(item.id)">
                                    <v-icon>mdi-delete</v-icon>
                                </v-btn>
                            </td>
                            
                            <!-- Edit/Save button -->
                            <td class="table-icon fixed-width">
                                <v-btn variant="plain" icon
                                    @click="editCustomerId === item.id ? saveCustomer() : editCustomerDetails(item)">
                                    <v-icon>{{ editCustomerId === item.id ? 'mdi-content-save' : 'mdi-pencil' }}</v-icon>
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
    name: "CustomerTable",
    
    // Props for searching and filtering
    props: {
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
            // Basic data
            customers: [],                   // List of customers
            selectedCustomers: [],           //// Selected customers for mass operations
            customerToDelete: null,          // Customer ID for deletion
            
            // Interface state
            isAlertVisible: false,           // Modal window visibility
            loading: false,                  // Loading indicator
            totalItems: 0,                   // Total number of items
            
            // Table headers
            headers: [
                { title: 'Auswählen', key: 'checkbox', sortable: false, width: '80px' },
                { title: 'ID', key: 'id', sortable: true, align: 'start' },
                { title: 'Vorname', key: 'firstname', sortable: true },
                { title: 'Nachname', key: 'lastname', sortable: true },
                { title: 'Email', key: 'email', sortable: true },
                { title: 'Telefonnummer', key: 'phonenumber', sortable: true },
                { title: 'Straße und Hausnummer', key: 'addressline', sortable: true },
                { title: 'PLZ', key: 'postalcode', sortable: true },
                { title: 'Stadt', key: 'city', sortable: true },
                { title: 'Löschen', key: 'delete', sortable: false, width: '60px' },
                { title: 'Bearbeiten', key: 'edit', sortable: false, width: '60px' }
            ],
            
            // Customer data fields
            fields: ["id", "firstname", "lastname", "email", "phonenumber", "addressline", "postalcode", "city"],
            
            // Editing
            editCustomerId: null,            // ID of the edited client
            editCustomer: {},                // Editable client data
            fieldErrors: {},                 // Field validation errors
            
            // Pagination and sorting settings
            options: {
                page: 1,                     // Current page
                itemsPerPage: 20,           // Elements on the page
                sortBy: [{ key: 'id', order: 'desc' }]  // Default sorting
            },
            
            // Modal window
            alertHeading: '',               // Alert header
            alertParagraph: '',             // Alerte Text
            alertOkayButton: '',            // Confirmation button text
            confirmAction: null,            // Confirmation function
            
            // Search
            searchDebounceTimer: null       // Timer for debaunce search
        };
    },

    computed: {
        // Current page
        currentPage() {
            return this.options.page;
        },
        
        // Total number of pages
        totalPages() {
            return Math.ceil(this.totalItems / this.options.itemsPerPage);
        },
        
        // Editing mode flag
        isEditing() {
            return this.editCustomerId !== null;
        },
        
        // Array of page numbers for pagination
        pageItems() {
            return Array.from({ length: this.totalPages }, (_, i) => i + 1);
        }
    },

    // Tracking changes in props
    watch: {
        // Track changes to the search string
        searchString: {
            handler(newValue, oldValue) {
                // React only to real changes
                if (newValue !== oldValue) {
                    this.handleSearchChange(newValue);
                }
            },
            immediate: false
        },
        
        // Tracking the search status
        isSearchActive: {
            handler(newValue) {
                if (!newValue && !this.searchString) {
                    // Search deactivated, return to normal browsing
                    this.options.page = 1;
                    this.loadItems();
                }
            }
        }
    },

    // Loading data when mounting a component
    mounted() {
        this.loadItems();
    },

    methods: {
        // === SEARCH METHODS ===
        
        /**
         * Search string modification handling with debounce
         * @param {string} searchValue - New search value
         */
        handleSearchChange(searchValue) {
            // Clear the previous timer
            if (this.searchDebounceTimer) {
                clearTimeout(this.searchDebounceTimer);
            }

            // If the string is empty, load normal data
            if (!searchValue || !searchValue.trim()) {
                this.options.page = 1;
                this.loadItems();
                return;
            }

            // Set debounce to 300ms
            this.searchDebounceTimer = setTimeout(() => {
                this.options.page = 1; // Reset to the first page on a new search
                this.searchCustomers(searchValue);
            }, 300);
        },

        /**
         * Search clients by query
         * @param {string} query - Search query
         * @param {number} page - Page number
         */
        async searchCustomers(query = this.searchString, page = this.options.page) {
            if (!query || !query.trim()) {
                this.loadItems();
                return;
            }

            this.loading = true;
            try {
                console.log('Customer Search:', query, 'Page:', page);

                const params = {
                    query: query.trim(),
                    page: page,
                    itemsPerPage: this.options.itemsPerPage,
                    sortBy: this.options.sortBy.length > 0 ? this.options.sortBy[0].key : 'id',
                    sortDesc: this.options.sortBy.length > 0 ? this.options.sortBy[0].order === 'desc' : true
                };

                const response = await axios.get('/api/customers/search', { params });
                console.log('API Search Response:', response.data);

                // Handling different response formats
                let items = [];
                const totalItems = ref(0);
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

                this.customers = items;
                this.totalItems = total;
                this.options.page = page;

                console.log('Search results:', {
                    query,
                    itemsFound: items.length,
                    totalItems: total,
                    currentPage: page
                });

            } catch (error) {
                console.error('Error when searching for clients:', error);
                if (error.response) {
                    console.error('Response Data:', error.response.data);
                    console.error('Response Status:', error.response.status);
                }

                // При ошибке показываем пустые результаты
                this.customers = [];
                this.totalItems = 0;
                this.$emit('show-error', 'Error when searching for clients');
            } finally {
                this.loading = false;
            }
        },

        // === PAGINATION AND SORTING METHODS ===
        
        /**
         * Handling changes to table options (v-data-table-server colback)
         * @param {Object} newOptions - New table options
         */
        onOptionsUpdate(newOptions) {
            console.log('Updating the table options:', newOptions);
            
            let needsReload = false;

            // Handling page changes
            if (newOptions.page !== this.options.page) {
                this.options.page = newOptions.page;
                needsReload = true;
            }

            // Handling changes in the number of elements on the page
            if (newOptions.itemsPerPage !== this.options.itemsPerPage) {
                this.options.itemsPerPage = newOptions.itemsPerPage;
                this.options.page = 1; // Reset on first page
                needsReload = true;
            }

            // Processing a change in sorting
            if (newOptions.sortBy && JSON.stringify(newOptions.sortBy) !== JSON.stringify(this.options.sortBy)) {
                this.options.sortBy = newOptions.sortBy;
                this.options.page = 1; // Reset to the first page when sorting is changed
                needsReload = true;
            }

            // Reload data if changes have been made
            if (needsReload) {
                if (this.isSearchActive && this.searchString && this.searchString.trim()) {
                    this.searchCustomers(this.searchString, this.options.page);
                } else {
                    this.loadItems();
                }
            }
        },

        /**
         * Handling page changes
         * @param {number} page - New page number
         */
        handlePageChange(page) {
            this.options.page = page;

            // Determine whether to load search results or normal data
            if (this.isSearchActive && this.searchString && this.searchString.trim()) {
                this.searchCustomers(this.searchString, page);
            } else {
                this.loadItems();
            }
        },

        /**
         * Handling changes in the number of items on the page
         * @param {number} itemsPerPage - New number of items
         */
        handleItemsPerPageChange(itemsPerPage) {
            this.options.itemsPerPage = itemsPerPage;
            this.options.page = 1; // Reset to the first page

            if (this.isSearchActive && this.searchString && this.searchString.trim()) {
                this.searchCustomers(this.searchString, 1);
            } else {
                this.loadItems();
            }
        },

        // === VALIDATION METHODS ===
        
        /**
         * Getting validation rules for a field
         * @param {string} field - Field name
         * @returns {Array} Array of validation rules
         */
        getFieldRules(field) {
            switch (field) {
                case 'id':
                    return []; // ID not eddit
                case 'firstname':
                    return [value => !!value || 'Vorname ist erforderlich'];
                case 'lastname':
                    return [value => !!value || 'Nachname ist erforderlich'];
                case 'email':
                    return [
                        value => !!value || 'Email ist erforderlich',
                        value => /.+@.+\..+/.test(value) || 'Email muss gültig sein'
                    ];
                case 'phonenumber':
                    return [value => !!value || 'Telefonnummer ist erforderlich'];
                case 'addressline':
                    return [value => !!value || 'Adresse ist erforderlich'];
                case 'postalcode':
                    return [value => !!value || 'PLZ ist erforderlich'];
                case 'city':
                    return [value => !!value || 'Stadt ist erforderlich'];
                default:
                    return [];
            }
        },

        // === MODAL WINDOW METHODS ===
        
        /**
         * Display confirmation modal
         * @param {string} heading - Header
         * @param {string} paragraph - Message text
         * @param {string} okayButton - Text of the confirmation button
         * @param {Function} action - Confirmation function
         */
        showAlert(heading, paragraph, okayButton, action) {
            this.alertHeading = heading;
            this.alertParagraph = paragraph;
            this.alertOkayButton = okayButton;
            this.confirmAction = action;
            this.isAlertVisible = true;
        },

        /**
         * Handling confirmation in a modal window
         */
        handleConfirmation() {
            if (this.confirmAction) {
                this.confirmAction();
            }
            this.isAlertVisible = false;
        },

        // ===DELETION METHODS ===
        
        /**
         * Confirm deletion of one client
         * @param {number} id - client ID
         */
        confirmDeleteCustomer(id) {
            this.customerToDelete = id;
            this.showAlert(
                'Kunde löschen',
                'Möchten Sie den ausgewählten Kunden wirklich löschen?',
                'Löschen',
                this.deleteCustomer
            );
        },

        /**
         * Confirm deletion of selected clients
         */
        confirmDeleteSelectedCustomers() {
            if (this.selectedCustomers.length > 0) {
                const message = this.selectedCustomers.length === 1
                    ? 'Möchten Sie den ausgewählten Kunden wirklich löschen?'
                    : 'Möchten Sie die ausgewählten Kunden wirklich löschen?';

                const heading = this.selectedCustomers.length === 1
                    ? 'Kunde löschen'
                    : 'Kunden löschen';

                this.showAlert(
                    heading,
                    message,
                    'Löschen',
                    this.deleteCustomers
                );
            }
        },

        /**
         * Deleting one client
         */
        async deleteCustomer() {
            if (this.customerToDelete) {
                try {
                    await axios.delete(`/api/customers/${this.customerToDelete}`);
                    console.log('The client has been successfully deleted:', this.customerToDelete);

                    // Check if the elements remained on the current page after deletion
                    const isLastItemOnPage = this.customers.length === 1;
                    const isNotFirstPage = this.options.page > 1;

                    // If we delete the last element not on the first page, go to the previous page
                    if (isLastItemOnPage && isNotFirstPage) {
                        this.options.page -= 1;
                    }

                    // Reload the data after deletion
                    if (this.isSearchActive && this.searchString && this.searchString.trim()) {
                        await this.searchCustomers(this.searchString, this.options.page);
                    } else {
                        await this.loadItems();
                    }

                    // Remove the remote client from the selected ones
                    this.selectedCustomers = this.selectedCustomers.filter(id => id !== this.customerToDelete);
                    this.customerToDelete = null;
                } catch (error) {
                    console.error('Error when deleting a client:', error.response?.data || error.message);
                    this.$emit('show-error', 'Error when deleting a client');
                }
            }
        },

        /**
         * Deleting multiple clients
         */
        async deleteCustomers() {
            if (this.selectedCustomers.length > 0) {
                try {
                    await axios.delete(`/api/customers`, {
                        data: { ids: this.selectedCustomers }
                    });

                    // Logic of going to the previous page if we delete all elements
                    const itemsBeingDeleted = this.selectedCustomers.length;
                    const remainingItemsOnPage = this.customers.length - itemsBeingDeleted;
                    const isNotFirstPage = this.options.page > 1;

                    if (remainingItemsOnPage <= 0 && isNotFirstPage) {
                        this.options.page -= 1;
                    }

                    // Reload the data after deletion
                    if (this.isSearchActive && this.searchString && this.searchString.trim()) {
                        await this.searchCustomers(this.searchString, this.options.page);
                    } else {
                        await this.loadItems();
                    }

                    this.selectedCustomers = [];
                    this.$emit('customersDeleted');
                } catch (error) {
                    console.error('Error when deleting clients:', error);
                    this.$emit('show-error', 'Error when deleting clients');
                }
            }
        },

        // === EDITING METHODS ===
        
        /**
         * Client edit confirmation
         */
        async confirmEditCustomer() {
            try {
                // Prepare data to be sent
                const formattedData = {
                    firstname: this.editCustomer.firstname,
                    lastname: this.editCustomer.lastname,
                    email: this.editCustomer.email,
                    phonenumber: this.editCustomer.phonenumber,
                    addressline: this.editCustomer.addressline,
                    postalcode: this.editCustomer.postalcode,
                    city: this.editCustomer.city,
                    company: this.editCustomer.company
                };

                await axios.put(`/api/customers/${this.editCustomerId}`, formattedData);
                this.cancelEdit();

                // Reload data after saving
                if (this.isSearchActive && this.searchString && this.searchString.trim()) {
                    await this.searchCustomers(this.searchString, this.options.page);
                } else {
                    await this.loadItems();
                }
            } catch (error) {
                console.error('Error when saving a client:', error.response?.data || error.message);
                this.$emit('show-error', 'Error when saving a client');
            }
        },

        /**
         * Start editing client
         * @param {Object} customer - Customer object
         */
        editCustomerDetails(customer) {
            this.editCustomerId = customer.id;
            this.editCustomer = { ...customer };
        },

        /**
         * Saving the client (confirmation call)
         */
        async saveCustomer() {
            try {
                // Prepare data to be sent
                const formattedData = {
                    firstname: this.editCustomer.firstname,
                    lastname: this.editCustomer.lastname,
                    email: this.editCustomer.email,
                    phonenumber: this.editCustomer.phonenumber,
                    addressline: this.editCustomer.addressline,
                    postalcode: this.editCustomer.postalcode,
                    city: this.editCustomer.city,
                    company: this.editCustomer.company
                };

                await axios.put(`/api/customers/${this.editCustomerId}`, formattedData);
                this.cancelEdit();

                // Reload data after saving
                if (this.isSearchActive && this.searchString && this.searchString.trim()) {
                    await this.searchCustomers(this.searchString, this.options.page);
                } else {
                    await this.loadItems();
                }
            } catch (error) {
                console.error('Error when saving a client:', error.response?.data || error.message);
                this.$emit('show-error', 'Error when saving a client');
            }
        },

        /**
         * Cancel edit
         */
        cancelEdit() {
            this.editCustomerId = null;
            this.editCustomer = {};
            this.fieldErrors = {};
        },

        // === DATA DOWNLOAD METHODS ===
        
        /**
         * Downloading items from the server
         */
        async loadItems() {
            this.loading = true;

            try {
                const params = {
                    page: this.options.page,
                    itemsPerPage: this.options.itemsPerPage
                };

                // Add sorting parameters
                if (this.options.sortBy && this.options.sortBy.length > 0) {
                    params.sortBy = this.options.sortBy[0].key;
                    params.sortDesc = this.options.sortBy[0].order === 'desc';
                } else {
                    // Default sorting: new first
                    params.sortBy = 'id';
                    params.sortDesc = false;
                }

                console.log('Request parameters:', params);

                const response = await axios.get('/api/customers', { params });
                console.log('API Response:', response.data);

                this.customers = response.data.items || response.data || [];
                this.totalItems = response.data.total || response.data.totalItems || this.customers.length;
            } catch (error) {
                console.error('Error during data loading:', error);
                if (error.response) {
                    console.error('Response Data:', error.response.data);
                }
                this.customers = [];
                this.totalItems = 0;
            } finally {
                this.loading = false;
            }
        }
    },

    // Cleanup when unmounting a component
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
    margin-right: 7vh;
}

.spacer {
    flex-grow: 1;
}

.table-container {
    position: relative;
    width: 95%;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    max-width: 100%;
    margin-right: 20px;
}

.scrollable-table {
    max-height: 75vh;
    overflow-y: scroll;
    background-color: #fff;
    width: 90vw;
    max-width: 100%;
}

.fixed-width {
    width: 150px;
    padding: 12px 16px;
}

.table-icon {
    width: 48px;
    text-align: center;
}

.pagination-container {
    background-color: #fff;
    padding: 12px;
    border-top: 1px solid #edf2f7;
}

.loading-indicator {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 100;
}

/* Markierung für die bearbeitete Zeile */
.edited-row {
    background-color: #e3f2fd !important;
}

/* Tabellenstil */
:deep(.customer-table) {
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

/* Bearbeitungsfelder */
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