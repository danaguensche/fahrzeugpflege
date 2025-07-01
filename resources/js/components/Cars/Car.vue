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

        <div class="content-container">
            <DefaultButton @click="toggleCarForm">Fahrzeug hinzufügen</DefaultButton>
        </div>

        <div class="form-container">
            <CarForm :isOpen="showCarForm" @close="showCarForm = false" />
        </div>

        <CarTable :cars="cars" :editCarId="editCarId" :editCar="editCar" :currentPage="currentPage"
            :totalPages="totalPages" :searchString="searchText" :isSearchActive="isSearchActive" 
            @edit-car="editCarDetails" @save-car="saveCar" @page-changed="changePage" 
            @update:options="handleSortChange" />
    </div>
</template>

<script>
import axios from 'axios';
import { mapState } from 'vuex';
import Search from '../CommonSlots/Searchbar.vue';
import DefaultButton from '../CommonSlots/DefaultButton.vue';
import CarForm from './addCar/CarForm.vue';
import CarTable from './CarTable.vue';
import CloseButton from '../CommonSlots/CloseButton.vue';

export default {
    name: 'Car',

    components: {
        Search,
        DefaultButton,
        CarForm,
        CarTable,
        CloseButton
    },
    
    data() {
        return {
            //Search
            searchContext: "Suchen Sie nach einem Fahrzeug...",
            searchText: '',
            isSearchActive: false,
            searchDebounceTimer: null,

            showCarForm: false,

            //Car Data
            cars: [],
            editCarId: null,
            editCar: {},

            //Pagination
            currentPage: 1,
            totalPages: 1,
            totalItems: 0,
            itemsPerPage: 20,

            loading: false,
        }
    },

    computed: {
        ...mapState(['isSidebarOpen'])
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
        this.loadCars();
    },
    
    methods: {
        // UI Actions
        toggleCarForm() {
            this.showCarForm = !this.showCarForm;
        },

        changePage(page) {
            if (this.isSearchActive) {
                this.searchCars(page);
            } else {
                this.loadCars(page);
            }
        },

        editCarDetails(car) {
            this.editCarId = car.Kennzeichen;
            this.editCar = { ...car };
        },

        cancelEdit() {
            this.editCarId = null;
            this.editCar = {};
        },

        //Data loading
        async loadCars(page = 1) {
            try {
                const response = await axios.get(`/api/cars`, {
                    params: {
                        page: page,
                        itemsPerPage: this.itemsPerPage,
                    }
                });

                this.cars = response.data.items || [];
                this.totalItems = response.data.totalItems || 0;
                this.totalPages = Math.ceil(this.totalItems / this.itemsPerPage);
                this.currentPage = page;
                this.isSearchActive = false;
                
                console.log('LoadCars:', {
                    page,
                    totalItems: this.totalItems,
                    totalPages: this.totalPages,
                    carsCount: this.cars.length
                });
            } catch (error) {
                console.error('Fehler beim Abrufen der Fahrzeuge:', error);
            }
        },

        async saveCar(kennzeichen) {
            try {
                await axios.put(`/api/fahrzeuge/${kennzeichen}`, this.editCar);
                if (this.isSearchActive) {
                    await this.searchCars(this.currentPage);
                } else {
                    await this.loadCars(this.currentPage);
                }
                this.cancelEdit();
                this.editCarId = null;
                this.editCar = {};
            } catch (error) {
                console.error('Fehler beim Speichern des Fahrzeugs:', error);
            }
        },

        //Search Handling
        clearSearch() {
            this.searchText = '';
            this.isSearchActive = false;
            this.clearSearchTimer();
            this.loadCars(1);
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
                this.searchCars(1);
            }, 300);
        },

        clearSearchTimer() {
            if (this.searchDebounceTimer) {
                clearTimeout(this.searchDebounceTimer);
                this.searchDebounceTimer = null;
            }
        },

        async searchCars(page = 1) {
            if (!this.searchText?.trim()) {
                this.clearSearch();
                return;
            }
            
            try {
                const response = await axios.get('/api/cars/search', {
                    params: {
                        query: this.searchText,
                        page,
                        per_page: this.itemsPerPage
                    }
                });

                const { items, totalItems } = this.extractDataFromResponse(response.data);
                
                this.cars = items || [];
                this.totalItems = totalItems || 0;
                this.totalPages = Math.ceil(this.totalItems / this.itemsPerPage);
                this.currentPage = page;
                this.isSearchActive = true;
                
                console.log('SearchCars:', {
                    searchText: this.searchText,
                    page,
                    totalItems: this.totalItems,
                    totalPages: this.totalPages,
                    carsCount: this.cars.length
                });
            } catch (error) {
                console.error('Fehler beim Suchen der Fahrzeuge:', error);
                this.resetCarData(page);
            }
        },

        async fetchData(url, params, isSearch) {
            this.loading = true;
            this.isSearchActive = isSearch;

            try {
                const response = await axios.get(url, { params });
                this.processApiResponse(response.data, params.page);
            } catch (error) {
                console.error(`Fehler beim ${isSearch ? 'Suchen' : 'Laden'} der Fahrzeuge:`, error);
                this.resetCarData(params.page);
            } finally {
                this.loading = false;
            }
        },

        processApiResponse(data, page) {
            console.log('Processing API Response:', data);
            const { items, totalItems } = this.extractDataFromResponse(data);

            this.cars = items || [];
            this.totalItems = totalItems || 0;
            this.currentPage = page || 1;
            this.totalPages = Math.ceil(this.totalItems / this.itemsPerPage);

            console.log('Processed data:', {
                carsCount: this.cars.length,
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

        resetCarData(page = 1) {
            this.cars = [];
            this.totalItems = 0;
            this.totalPages = 1;
            this.currentPage = page;
        },
    }
}
</script>

<style scoped>
.car-page {
    display: flex;
    align-items: flex-start;
    justify-content: flex-start;
    flex-direction: column;
    margin-left: 150px;
    margin-right: 50px;
    transition: margin-left 0.3s ease;
    font-family: var(--font-family);
}

.content-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    margin-top: -80px;
    z-index: 5;
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

.form-container {
    margin-top: 1vh;
    margin-bottom: 1vh;
}

.car-page-sidebar-opened {
    margin-left: 330px;
    transition: margin-left 0.3s ease;
}

@media only screen and (max-width: 650px) {
    .car-page {
        margin-left: 160px;
        font-size: 12px;
    }

    .car-page-sidebar-opened {
        margin-left: 260px;
    }

    .content-container {
        flex-direction: column;
    }

    .form-container {
        width: 100%;
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