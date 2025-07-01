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

        <CustomerTable :searchString="searchText" :isSearchActive="isSearchActive"
            @customersDeleted="handleCustomersDeleted" @show-error="handleError"></CustomerTable>
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
            searchContext: "Suchen Sie nach einem Kunden...",
            searchText: '',
            isSearchActive: false,
            searchDebounceTimer: null,
            showCustomerForm: false,


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

        // UI Actions
        toggleCustomerForm() {
            this.showCustomerForm = !this.showCustomerForm;
        },

        handleCustomersDeleted() {
            // Wird von CustomerTable emittiert nach erfolgreichem Löschen
            console.log('Customers deleted, table will refresh automatically');
        },

        handleError(message) {
            console.error('Error from CustomerTable:', message);
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