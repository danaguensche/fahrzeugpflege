<template>
    <div class="jobs-page" :class="{ 'jobs-page-sidebar-opened': isSidebarOpen }">
        <div class="search-wrapper">
            <div class="search-input-container">
                <Search :context="searchContext" v-model="searchText" @clearSearch="clearSearch" />
                <div class="search-buttons">
                    <v-btn :icon="true" :prepend-icon="'mdi-magnify'" class="search-button" variant="text"
                        @click="searchJobs">
                        <v-icon>mdi-magnify</v-icon>
                    </v-btn>
                    <CloseButton :isVisible="searchText.length > 0" class="close-button" @click="clearSearch">
                    </CloseButton>
                </div>
            </div>
        </div>

        <div class="content-container">
            <DefaultButton @click="openAddJobDialog">Auftrag hinzufügen</DefaultButton>
        </div>

        <DataTable ref="jobDataTable" :searchString="searchText" :isSearchActive="isSearchActive" endpoint="jobs"
            :headers="filteredJobHeaders" :fields="jobFields" itemKey="id" detailsPage="jobdetails"
            detailsUrlBasePath="auftraege" deleteKey="ids" @itemsDeleted="handleJobsDeleted"
            @show-error="handleError" :canEditStatusOnly="userRole === 'trainee'" />

        <AddJobForm v-model="showAddJobDialog" @job-added="handleJobAdded" />

    </div>
</template>

<script>
import DataTable from '../Table/DataTable.vue';
import { mapState } from 'vuex';
import Search from '../CommonSlots/Searchbar.vue';
import CloseButton from '../CommonSlots/CloseButton.vue';
import AddJobForm from './AddJobForm.vue';
import DefaultButton from '../CommonSlots/DefaultButton.vue';

export default {
    name: 'Jobs',
    components: {
        DataTable,
        Search,
        CloseButton,
        AddJobForm,
        DefaultButton
    },

    data() {
        return {
            //Search
            searchContext: "Suchen Sie nach einem Auftrag...",
            searchText: '',
            isSearchActive: false,
            searchDebounceTimer: null,
            jobHeaders: [
                { title: 'Auswählen', key: 'select', sortable: false, width: '60px' },
                { title: 'id', key: 'id', sortable: true, align: 'start' },
                { title: 'Titel', key: 'title', sortable: true },
                { title: 'Beschreibung', key: 'description', sortable: true },
                { title: 'Abholtermin', key: 'scheduled_at', sortable: true },
                {
                    title: 'Status', key: 'status', sortable: true, editable: true, type: 'select', options: [
                        { title: 'Ausstehend', value: 'ausstehend' },
                        { title: 'In Bearbeitung', value: 'in_bearbeitung' },
                        { title: 'im Rückblick', value: 'im_rueckblick' },
                        { title: 'Abgeschlossen', value: 'abgeschlossen' },
                    ]
                },
                { title: 'Services', key: 'services', sortable: false },
                { title: 'Löschen', key: 'delete', sortable: false },
                { title: 'Bearbeiten', key: 'edit', sortable: false }
            ],
            jobFields: ["id", "title", "description", "scheduled_at", "status", "services"],
            showAddJobDialog: false,
            selectedStatus: null,
            selectedCustomer: null,
            selectedCar: null,
            selectedUser: null,
            startDate: null,
            endDate: null,
            customers: [],
            cars: [],
            users: [],
            customersLoading: false,
            carsLoading: false,
            usersLoading: false,
            customerSearchTimeout: null,
            carSearchTimeout: null,
            userSearchTimeout: null,
        }
    },

    computed: {
        ...mapState(['isSidebarOpen']),
        ...mapState('auth', ['userRole']),
        filteredJobHeaders() {
            if (this.userRole === 'trainee') {
                return this.jobHeaders.filter(header =>
                    header.key !== 'select' &&
                    header.key !== 'delete'
                );
            }
            return this.jobHeaders;
        },
        activeFilters() {
            const filters = {};
            if (this.selectedStatus) {
                filters.status = this.selectedStatus;
            }
            if (this.selectedCustomer) {
                filters.customer_id = this.selectedCustomer.id;
            }
            if (this.selectedCar) {
                filters.car_id = this.selectedCar.id;
            }
            if (this.selectedUser) {
                filters.user_id = this.selectedUser.id;
            }
            if (this.startDate) {
                filters.start = this.startDate;
            }
            if (this.endDate) {
                filters.end = this.endDate;
            }
            return filters;
        }
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
        applyFilters() {
            this.$refs.jobDataTable.loadItems();
        },

        async fetchCustomers(query = '') {
            this.customersLoading = true;
            try {
                const response = await axios.get(`/api/customers/search?query=${query}`);
                this.customers = response.data.data.map(customer => ({
                    id: customer.id,
                    firstname: customer.firstname,
                    lastname: customer.lastname,
                    full_name: `${customer.firstname} ${customer.lastname}`,
                    email: customer.email,
                }));
            } catch (error) {
                console.error('Error fetching customers:', error);
            } finally {
                this.customersLoading = false;
            }
        },

        searchCustomers(query) {
            if (this.customerSearchTimeout) {
                clearTimeout(this.customerSearchTimeout);
            }
            this.customerSearchTimeout = setTimeout(() => {
                this.fetchCustomers(query);
            }, 300);
        },

        async fetchCars(query = '') {
            this.carsLoading = true;
            try {
                const response = await axios.get(`/api/cars/search?query=${query}`);
                this.cars = response.data.data.map(car => ({
                    id: car.id,
                    Kennzeichen: car.Kennzeichen,
                    Automarke: car.Automarke,
                }));
            } catch (error) {
                console.error('Error fetching cars:', error);
            } finally {
                this.carsLoading = false;
            }
        },

        searchCars(query) {
            if (this.carSearchTimeout) {
                clearTimeout(this.carSearchTimeout);
            }
            this.carSearchTimeout = setTimeout(() => {
                this.fetchCars(query);
            }, 300);
        },

        async fetchUsers(query = '') {
            this.usersLoading = true;
            console.log('Fetching users with role:', this.userRole);
            try {
                const response = await axios.get(`/api/users/search?query=${query}`);
                this.users = response.data.data.map(user => ({
                    id: user.id,
                    firstname: user.firstname,
                    lastname: user.lastname,
                    full_name: `${user.firstname} ${user.lastname}`,
                    email: user.email,
                }));
            } catch (error) {
                console.error('Error fetching users:', error);
            } finally {
                this.usersLoading = false;
            }
        },

        searchUsers(query) {
            if (this.userSearchTimeout) {
                clearTimeout(this.userSearchTimeout);
            }
            this.userSearchTimeout = setTimeout(() => {
                this.fetchUsers(query);
            }, 300);
        },

        openAddJobDialog() {
            this.showAddJobDialog = true;
        },

        handleJobAdded() {
            this.showAddJobDialog = false;
            this.$refs.jobDataTable.loadItems();
        },

        handleJobsDeleted() {
            // Wird von JobsTable emittiert nach erfolgreichem Löschen
            console.log('Jobs deleted, table will refresh automatically');
        },

        handleError(message) {
            console.error('Error from JobTable:', message);
            // Hier können Sie eine Toast-nachricht oder ähnliches anzeigen
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

        searchJobs() {
            if (this.searchText?.trim()) {
                this.isSearchActive = true;
            }
        }
    },
    mounted() {
        console.log('Jobs.vue mounted. userRole:', this.userRole);
        this.fetchCustomers();
        this.fetchCars();
        if (this.userRole !== 'trainee') {
            this.fetchUsers();
        }
    }
}
</script>

<style scoped>
.jobs-page {
    margin-left: 150px;
    padding-right: 20px;
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
    z-index: 10;
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

.jobs-page-sidebar-opened {
    margin-left: 330px;
    transition: margin-left 0.3s ease;
}

/* Tablet Styles */
@media only screen and (max-width: 1024px) {
    .jobs-page {
        margin-left: 120px;
    }
}

/* Mobile Styles */
@media only screen and (max-width: 768px) {
    .jobs-page {
        margin-left: 160px;
        font-size: 12px;
    }

    .jobs-page-sidebar-opened {
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