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
            <v-btn class="add-job-button" color="primary" @click="openAddJobDialog">
                <v-icon left>mdi-plus</v-icon>
                Neue Aufgabe
            </v-btn>
        </div>
        <DataTable
            :searchString="searchText"
            :isSearchActive="isSearchActive"
            endpoint="jobs"
            :headers="jobHeaders"
            :fields="jobFields"
            itemKey="id"
            detailsPage="jobdetails"
            detailsUrlBasePath="jobs"
            deleteKey="id"
            @itemsDeleted="handleJobsDeleted"
            @show-error="handleError"
        />

        <AddJobForm v-model="showAddJobDialog" @job-added="handleJobAdded" />

    </div>
</template>

<script>
import DataTable from '../Table/DataTable.vue';
import { mapState } from 'vuex';
import Search from '../CommonSlots/Searchbar.vue';
import CloseButton from '../CommonSlots/CloseButton.vue';
import AddJobForm from './AddJobForm.vue';

export default {
    name: 'Jobs',
    components: {
        DataTable,
        Search,
        CloseButton,
        AddJobForm,
    },

    data() {
        return {
            //Search
            searchContext: "Suchen Sie nach einem Auftrag...",
            searchText: '',
            isSearchActive: false,
            searchDebounceTimer: null,
            jobHeaders: [
                { title: 'Auswählen', key: 'checkbox', sortable: false, width: '80px' },
                { title: 'id', key: 'id', sortable: true, align: 'start' },
                { title: 'Titel', key: 'title', sortable: true },
                { title: 'Beschreibung', key: 'description', sortable: true },
                { title: 'Abholtermin', key: 'scheduled_at', sortable: true },
                { title: 'Status', key: 'status', sortable: true },
            ],
            jobFields: ["id", "title", "description", "scheduled_at", "status"],
            showAddJobDialog: false,
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

    methods: {
        openAddJobDialog() {
            this.showAddJobDialog = true;
        },

        handleJobAdded() {
            this.showAddJobDialog = false;
            // Refresh the DataTable to show the new job
            this.$children[1].loadItems(); // Assuming DataTable is the second child
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
    }
}
</script>

<style scoped>
.jobs-page {
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
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.search-input-container {
    position: relative;
    display: flex;
    align-items: center;
    flex-grow: 1;
    margin-right: 20px;
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

.jobs-page-sidebar-opened {
    margin-left: 330px;
    transition: margin-left 0.3s ease;
}

.add-job-button {
    margin-left: auto; /* Pushes the button to the right */
}

@media only screen and (max-width: 650px) {
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