<template>
    <div class="users-page" :class="{ 'users-page-sidebar-opened': isSidebarOpen }">
        <div class="search-wrapper">
            <div class="search-input-container">
                <Search :context="searchContext" v-model="searchText" @clearSearch="clearSearch" />
                <div class="search-buttons">
                    <v-btn :icon="true" :prepend-icon="'mdi-magnify'" class="search-button" variant="text"
                        @click="searchUsers">
                        <v-icon>mdi-magnify</v-icon>
                    </v-btn>
                    <CloseButton :isVisible="searchText.length > 0" class="close-button" @close="clearSearch">
                    </CloseButton>
                </div>
            </div>
        </div>

        <div class="table-container">
            <DataTable :searchString="searchText" :isSearchActive="isSearchActive" endpoint="users"
                :headers="userHeaders" :fields="userFields" itemKey="id" deleteKey="id"
                @itemsDeleted="handleItemsDeleted" @show-error="handleError" :dataCleaner="cleanUserData" />
        </div>
    </div>
</template>


<script>
import DataTable from '../Table/DataTable.vue';
import Search from '../CommonSlots/Searchbar.vue';
import CloseButton from '../CommonSlots/CloseButton.vue';
import { mapState } from 'vuex';

export default {
    name: 'Users',
    components: {
        DataTable,
        Search,
        CloseButton
    },
    data() {
        return {
            userHeaders: [
                { title: 'Auswählen', key: 'select', sortable: false, width: '60px' },
                { title: 'Vorname', key: 'firstname' },
                { title: 'Nachname', key: 'lastname' },
                { title: 'Email', key: 'email' },
                { title: 'Telefon', key: 'phonenumber' },
                { title: 'Straße und Hausnummer', key: 'addressline' },
                { title: 'PLZ', key: 'postalcode' },
                { title: 'Stadt', key: 'city' },
                {
                    title: 'Rolle',
                    key: 'role',
                    type: 'select',
                    options: [
                        { title: 'Admin', value: 'admin' },
                        { title: 'Mitarbeiter', value: 'trainer' },
                        { title: 'Auszubildender', value: 'trainee' },
                    ],
                },
                { title: 'Löschen', key: 'delete', sortable: false, width: '60px' },
                { title: 'Bearbeiten', key: 'edit', sortable: false, width: '60px' }
            ],
            userFields: [
                'firstname',
                'lastname',
                'email',
                'phonenumber',
                'addressline',
                'postalcode',
                'city',
                'role',
            ],
            searchContext: "Suchen Sie nach einem Benutzer...",
            searchText: '',
        };
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
        cleanUserData(userData) {
            const fields = ['firstname', 'lastname', 'email', 'phonenumber', 'addressline', 'postalcode', 'city'];
            const cleanedData = { ...userData };

            fields.forEach(field => {
                if (cleanedData[field] == 'Nicht verfügbar') {
                    cleanedData[field] = '';
                }
            });

            return cleanedData;
        },

        getRoleColor(role) {
            switch (role) {
                case 'admin':
                    return 'red';
                case 'trainer':
                    return 'blue';
                case 'trainee':
                    return 'green';
                default:
                    return 'grey';
            }
        },
        handleUserAdded() {
            this.showAddUserDialog = false;
        },

        openAddUserDialog() {
            this.showAddUserDialog = true;
        },

        handleItemsDeleted() {
            // Wird von DataTable emittiert nach erfolgreichem Löschen
            console.log('Users deleted, table will refresh automatically');
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

        searchUsers() {
            if (this.searchText?.trim()) {
                this.isSearchActive = true;
            }
        }
    },
};
</script>

<style scoped>
.users-page {
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

.users-page-sidebar-opened {
    margin-left: 330px;
    transition: margin-left 0.3s ease;
}

/* Tablet Styles */
@media only screen and (max-width: 1024px) {
    .users-page {
        margin-left: 120px;
        margin-right: 30px;
    }
}

/* Mobile Styles */
@media only screen and (max-width: 768px) {
    .users-page {
        margin-left: 160px;
        margin-right: 20px;
        font-size: 12px;
    }

    .users-page-sidebar-opened {
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