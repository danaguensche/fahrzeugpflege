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
            <DefaultButton @click="toggleCustomerForm">Kunde hinzufügen</DefaultButton>
        </div>

        <div class="form-container">
            <CustomerForm :isOpen="showCustomerForm" @close="showCustomerForm = false" />
        </div>

        <CustomerTable :customers="customers" :editCustomerId="editCustomerId" :editCustomer="editCustomer"
            :currentPage="currentPage" :totalPages="totalPages" :searchString="searchText"
            :isSearchActive="isSearchActive" @edit-customer="editCustomerDetails" @save-customer="saveCustomer"
            @page-changed="changePage" @update:options="handleSortChange" />
    </div>
</template>

<script>
import CloseButton from '../CommonSlots/CloseButton.vue';
import Search from '../CommonSlots/Searchbar.vue';
import DefaultButton from '../CommonSlots/DefaultButton.vue';
import CustomerForm from './addCustomer/CustomerForm.vue';
import CustomerTable from './CustomerTable.vue';
import { mapState } from 'vuex';

export default {
    name: "Customer",

    components: {
        Search,
        CloseButton,
        DefaultButton,
        CustomerForm,
        CustomerTable,
    },

    data() {
        return {
            //Search
            searchContext: "Suchen Sie nach einem Kunden...",
            searchText: '',
            isSearchActive: false,
            searchDebounceTimer: null,

            showCustomerForm: false,

            // Customer data
            customers: [],
            editCustomer: {},
            editCustomerId: null,

            // Pagination
            currentPage: 1,
            totalPages: 1,
            totalItems: 0,
            itemsPerPage: 20,

            loading: false,
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

    mounted() {
        this.loadCustomers();
    },

    methods: {
        // UI Actions
        toggleCustomerForm() {
            this.showCustomerForm = !this.showCustomerForm;
        },

        changePage(page) {
            if (this.isSearchActive) {
                this.searchCustomers(page);
            } else {
                this.loadCustomers(page);
            }
        },

        editCustomerDetails(customer) {
            this.editCustomerId = customer.id;
            this.editCustomer = { ...customer };
        },

        cancelEdit() {
            this.editCustomerId = null;
            this.editCustomer = {};
        },

        // Data loading
        async loadCustomers(page = 1) {
            try {
                const response = await axios.get(`/api/customers`, {
                    params: {
                        page: page,
                        itemsPerPage: this.itemsPerPage,
                    }
                });

                this.customers = response.data.items || [];
                this.totalItems = response.data.totalItems || 0;
                this.totalPages = Math.ceil(this.totalItems / this.itemsPerPage);
                this.currentPage = page;
                this.isSearchActive = false;
                
                console.log('LoadCustomers:', {
                    page,
                    totalItems: this.totalItems,
                    totalPages: this.totalPages,
                    customersCount: this.customers.length
                });
            } catch (error) {
                console.error('Fehler beim Abrufen der Kundendaten:', error);
            }
        },

        // Customer operations
        async saveCustomer(id) {
            try {
                await axios.put(`/api/customers/${id}`, this.editCustomer);
                if (this.isSearchActive) {
                    await this.searchCustomers(this.currentPage);
                } else {
                    await this.loadCustomers(this.currentPage);
                }
                this.cancelEdit();
                this.editCustomerId = null;
                this.editCustomer = {};
            } catch (error) {
                console.error('Fehler beim Speichern des Kunden:', error);
            }
        },

        // Search handling
        clearSearch() {
            this.searchText = '';
            this.isSearchActive = false;
            this.clearSearchTimer();
            this.loadCustomers(1);
        },

        handleSearchInput(newValue) {
            if (this.searchDebounceTimer) {
                clearTimeout(this.searchDebounceTimer);
            }

            if (!newValue?.trim()) {
                this.clearSearch();
                return;
            }

            this.searchDebounceTimer = setTimeout(() => {
                this.searchCustomers(1);
            }, 300);
        },

        clearSearchTimer() {
            if (this.searchDebounceTimer) {
                clearTimeout(this.searchDebounceTimer);
                this.searchDebounceTimer = null;
            }
        },

        async searchCustomers(page = 1) {
            if (!this.searchText?.trim()) {
                this.clearSearch();
                return;
            }
            
            try {
                const response = await axios.get('/api/customers/search', {
                    params: {
                        query: this.searchText,
                        page,
                        per_page: this.itemsPerPage
                    }
                });

                const { items, totalItems } = this.extractDataFromResponse(response.data);
                
                this.customers = items || [];
                this.totalItems = totalItems || 0;
                this.totalPages = Math.ceil(this.totalItems / this.itemsPerPage);
                this.currentPage = page;
                this.isSearchActive = true;
                
                console.log('SearchCustomers:', {
                    searchText: this.searchText,
                    page,
                    totalItems: this.totalItems,
                    totalPages: this.totalPages,
                    customersCount: this.customers.length
                });
            } catch (error) {
                console.error('Fehler beim Suchen der Kunden:', error);
                this.resetCustomerData(page);
            }
        },

        async fetchData(url, params, isSearch) {
            this.loading = true;
            this.isSearchActive = isSearch;

            try {
                const response = await axios.get(url, { params });
                this.processApiResponse(response.data, params.page);
            } catch (error) {
                console.error(`Fehler beim ${isSearch ? 'Suchen' : 'Laden'} der Kunden:`, error);
                this.resetCustomerData(params.page);
            } finally {
                this.loading = false;
            }
        },

        processApiResponse(data, page) {
            console.log('Processing API Response:', data);
            const { items, totalItems } = this.extractDataFromResponse(data);

            this.customers = items || [];
            this.totalItems = totalItems || 0;
            this.currentPage = page || 1;
            this.totalPages = Math.ceil(this.totalItems / this.itemsPerPage);

            console.log('Processed data:', {
                customersCount: this.customers.length,
                totalItems: this.totalItems,
                currentPage: this.currentPage,
                totalPages: this.totalPages
            });
        },

        extractDataFromResponse(data) {
            // Handle various API response formats
            if (data?.items) {
                return { items: data.items, totalItems: data.totalItems || 0 };
            }

            if (data?.data && Array.isArray(data.data)) {
                return { items: data.data, totalItems: data.total || data.data.length };
            }

            if (Array.isArray(data)) {
                return { items: data, totalItems: data.length };
            }

            return { items: [], totalItems: 0 };
        },

        resetCustomerData(page = 1) {
            this.customers = [];
            this.totalItems = 0;
            this.totalPages = 1;
            this.currentPage = page;
        },

    }
}
</script>

<style scoped>
.customer-page {
    display: flex;
    align-items: flex-start;
    justify-content: flex-start;
    flex-direction: column;
    margin-left: 150px;
    margin-right: 50px;
    transition: margin-left 0.3s ease;
    font-family: var(--font-family);
}

.customer-page-sidebar-opened {
    margin-left: 330px;
}

.search-wrapper {
    position: relative;
    z-index: 10;
    width: 100%;
    margin-bottom: 20px;
}

.search-input-container {
    position: relative;
    display: flex;
    align-items: center;
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

.close-button {
    z-index: 10;
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

.content-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    margin-top: -80px;
    z-index: 5;
}

.form-container {
    margin-top: 1vh;
    margin-bottom: 1vh;
}
</style>