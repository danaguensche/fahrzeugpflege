<template>
    <div class="car-page" :class="{ 'car-page-sidebar-opened': isSidebarOpen }">
        <div class="search-wrapper">
            <div class="search-input-container">
                <Search :context="searchContext" v-model="searchText" @clearSearch="clearSearch" />
                <div class="search-buttons">
                    <v-btn :icon="true" :prepend-icon="'mdi-magnify'" class="search-button" variant="text"
                        @click="searchCars">
                        <v-icon>mdi-magnify</v-icon>
                    </v-btn>
                    <CloseButton :isVisible="searchText.length > 0" class="close-button" @close="clearSearch">
                    </CloseButton>
                </div>
            </div>
        </div>

        <div class="table-container">
            <DataTable :buttonFunction="openAddCarDialog" addButtonLabel="Fahrzeug Hinzufügen"
                :searchString="searchText" :isSearchActive="isSearchActive" endpoint="cars"
                :headers="filteredCarHeaders" :fields="carFields" itemKey="Kennzeichen" detailsPage="fahrzeugdetails"
                detailsUrlBasePath="fahrzeuge" deleteKey="kennzeichen" :useExternalEdit="true"
                @itemsDeleted="handleItemsDeleted" @show-error="handleError" @edit-item="handleEditItem" />
        </div>

        <AddCarForm v-model="showAddCarDialog" @car-added="handleCarAdded" />
        <EditCarForm v-model="showEditCarDialog" :carData="selectedCar" @car-edited="handleCarEdited" />
    </div>
</template>

<script>
import { mapState, mapGetters } from 'vuex';
import Search from '../CommonSlots/Searchbar.vue';
import DefaultButton from '../CommonSlots/DefaultButton.vue';
import DataTable from '../Table/DataTable.vue';
import CloseButton from '../CommonSlots/CloseButton.vue';
import AddCarForm from './addCar/AddCarForm.vue';
import EditCarForm from './addCar/EditCarForm.vue';

export default {
    name: 'Car',

    components: {
        Search,
        DefaultButton,
        DataTable,
        CloseButton,
        AddCarForm,
        EditCarForm
    },

    data() {
        return {
            showAddCarDialog: false,
            showEditCarDialog: false,
            selectedCar: null,
            searchContext: "Suchen Sie nach einem Fahrzeug...",
            searchText: '',
            isSearchActive: false,
            searchDebounceTimer: null,
            carHeaders: [
                { title: 'Auswählen', key: 'select', sortable: false, width: '60px' },
                { title: 'Kennzeichen', key: 'Kennzeichen', sortable: true, align: 'start' },
                { title: 'Fahrzeugklasse', key: 'Fahrzeugklasse', sortable: true },
                { title: 'Automarke', key: 'Automarke', sortable: true },
                { title: 'Typ', key: 'Typ', sortable: true },
                { title: 'Farbe', key: 'Farbe', sortable: true },
                { title: 'Löschen', key: 'delete', sortable: false, width: '60px' },
                { title: 'Bearbeiten', key: 'edit', sortable: false, width: '60px' }
            ],
            carFields: ["Kennzeichen", "Fahrzeugklasse", "Automarke", "Typ", "Farbe"]
        }
    },

    computed: {
        ...mapState(['isSidebarOpen']),
        ...mapGetters('auth', ['isAdminOrTrainer']),
        filteredCarHeaders() {
            if (this.isAdminOrTrainer) {
                return this.carHeaders;
            } else {
                return this.carHeaders.filter(header => header.key !== 'delete' && header.key !== 'edit' && header.key !== 'select');
            }
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
        handleCarAdded() {
            this.showAddCarDialog = false;
        },

        handleCarEdited() {
            this.showEditCarDialog = false;
            this.selectedCar = null;
            if (this.$refs.DataTable) {
                this.$refs.DataTable.refresh();
            }
        },

        handleEditItem(item) {
            this.selectedCar = item;
            this.showEditCarDialog = true;
        },

        openAddCarDialog() {
            this.showAddCarDialog = true;
        },

        handleItemsDeleted() {
            console.log('Cars deleted, table will refresh automatically');
        },

        handleError(message) {
            console.error('Error from DataTable:', message);
        },

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

        searchCars() {
            if (this.searchText?.trim()) {
                this.isSearchActive = true;
            }
        }
    }
}
</script>

<style scoped>
.car-page {
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
    position: relative;
}

.content-container--trainee {
    margin-bottom: 40px;
    justify-content: flex-end;
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

.car-page-sidebar-opened {
    margin-left: 330px;
    transition: margin-left 0.3s ease;
}

@media only screen and (max-width: 1024px) {
    .car-page {
        margin-left: 120px;
        margin-right: 30px;
    }
}

@media only screen and (max-width: 768px) {
    .car-page {
        margin-left: 160px;
        margin-right: 20px;
        font-size: 12px;
    }

    .car-page-sidebar-opened {
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