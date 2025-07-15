<template>
    <div class="customer-page" :class="{ 'customer-page-sidebar-opened': isSidebarOpen }">
        <div class="search-wrapper">
            <div class="search-input-container">
                <Search :context="searchContext" v-model="searchText" @clearSearch="clearSearch" />
                <div class="search-buttons">
                    <v-btn :icon="true" :prepend-icon="'mdi-magnify'" class="search-button" variant="text"
                        @click="searchCustomers">
                        <v-icon>mdi-magnify</v-icon>
                    </v-btn>
                    <CloseButton :isVisible="searchText.length > 0" class="close-button" @close="clearSearch">
                    </CloseButton>
                </div>
            </div>
        </div>

        <div class="content-container">
            <DefaultButton @click="openAddCustomerDialog">Kunde hinzufügen</DefaultButton>
        </div>

        <DataTable :searchString="searchText" :isSearchActive="isSearchActive" endpoint="customers"
            :headers="customerHeaders" :fields="customerFields" itemKey="id" detailsPage="kundendetails"
            detailsUrlBasePath="kunden" @itemsDeleted="handleItemsDeleted" @show-error="handleError" />
        <AddCustomerForm v-model="showAddCustomerDialog" @customer-added="handleCustomerAdded" />

    </div>
</template>

<script>
import CloseButton from '../CommonSlots/CloseButton.vue';
import Search from '../CommonSlots/Searchbar.vue';
import DefaultButton from '../CommonSlots/DefaultButton.vue';
import DataTable from '../Table/DataTable.vue';
import AddCustomerForm from './addCustomer/AddCustomerForm.vue';
import { mapState } from 'vuex';

export default {
    name: "Customer",

    components: {
        Search,
        CloseButton,
        DefaultButton,
        AddCustomerForm,
        DataTable,
    },

    data() {
        return {
            showAddCustomerDialog: false,
            searchContext: "Suchen Sie nach einem Kunden...",
            searchText: '',
            isSearchActive: false,
            searchDebounceTimer: null,
            customerHeaders: [
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
            customerFields: ["id", "firstname", "lastname", "email", "phonenumber", "addressline", "postalcode", "city"]
        }
    },

    computed: {
        ...mapState(['isSidebarOpen']),
    },

    watch: {
        searchText: {
            handler(newValue) {
                this.handleSearchInput(newValue);
            },
            immediate: false
        }
    },

    methods: {

        openAddCustomerDialog() {
            this.showAddCustomerDialog = true;
        },

        handleCustomerAdded() {
            this.showAddCustomerDialog = false;
        },

        handleItemsDeleted() {
            // Wird von DataTable emittiert nach erfolgreichem Löschen
            console.log('Customers deleted, table will refresh automatically');
        },

        handleError(message) {
            console.error('Error from DataTable:', message);
            // Hier können Sie eine Toast-Nachricht oder ähnliches anzeigen
        },

        //Search Handling
        clearSearch() {
            this.searchText = '';
            this.isSearchActive = false;
            this.clearSearchTimer();
        },

        handleSearchInput(newValue) {
            if (this.searchDebounceTimer) {
                clearTimeout(this.searchDebounceTimer);
            }

            if (!newValue?.trim()) {
                this.clearSearch();
                return;
            }

            // Debounce für 300ms
            this.searchDebounceTimer = setTimeout(() => {
                this.isSearchActive = true;
            }, 300);
        },

        clearSearchTimer() {
            if (this.searchDebounceTimer) {
                clearTimeout(this.searchDebounceTimer);
                this.searchDebounceTimer = null;
            }
        },

        searchCustomers() {
            if (this.searchText?.trim()) {
                this.isSearchActive = true;
            }
        }
    }
}
</script>

<style scoped>
.customer-page {
    margin-left: 150px;
    margin-right: 50px;
    transition: margin-left 0.3s ease;
    font-family: var(--font-family);
}

.search-wrapper {
    position: relative;
    z-index: 10;
    width: 100%;
    margin-bottom: 10px;
}

.content-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    margin-top: -80px;
    z-index: 5;
    position: relative;
}

.table-container {
    width: 100%;
}

.search-input-container {
    position: relative;
    display: flex;
    align-items: center;
    width: 100%;
}

.search-buttons {
    display: flex;
    align-items: center;
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    gap: 4px;
    background: transparent;
    border-radius: 6px;
    padding: 2px;
}

.search-button {
    min-width: 36px !important;
    width: 36px;
    height: 36px;
    border-radius: 6px !important;
    transition: all 0.2s ease;
    background-color: transparent;
}

.search-button:hover {
    background-color: rgba(0, 0, 0, 0.04);
}

.search-button:disabled {
    opacity: 0.4;
    cursor: not-allowed;
}

.search-button .v-icon {
    font-size: 25px;
    color: #666;
}

.close-button {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 36px;
    width: 36px;
    height: 36px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s ease;
    background-color: transparent;
}

.close-button:hover {
    background-color: rgba(0, 0, 0, 0.04);
}



.table-container {
    width: 100%;
}

.customer-page-sidebar-opened {
    margin-left: 330px;
    transition: margin-left 0.3s ease;
}

/* Tablet Styles */
@media only screen and (max-width: 1024px) {
    .customer-page {
        margin-left: 120px;
        margin-right: 30px;
    }
}

/* Mobile Styles */
@media only screen and (max-width: 768px) {
    .customer-page {
        margin-left: 160px;
        margin-right: 20px;
        font-size: 12px;
    }

    .customer-page-sidebar-opened {
        margin-left: 260px;
    }

    .content-container {
        flex-direction: column;
    }

    .search-input-container {
        position: relative;
        display: flex;
        align-items: center;
    }

    .search-buttons {
        right: 6px;
        gap: 2px;
        padding: 1px;
    }

    .search-button,
    .close-button {
        min-width: 32px;
        width: 32px;
        height: 32px;
        border-radius: 4px;
    }

    .search-button .v-icon {
        font-size: 18px;
    }
}
</style>