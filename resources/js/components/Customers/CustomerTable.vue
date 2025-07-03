<template>
    <div class="component">
        <div class="header">
            <RefreshButton class="refresh-button" @refresh="loadItems"></RefreshButton>
            <div class="spacer"></div>

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

        <div class="table-container">
            <div v-if="loading" class="loader-container">
                <v-progress-circular indeterminate></v-progress-circular>
            </div>
            <div class="scrollable-table">
                <v-data-table-server :headers="headers" :items="customers" :server-items-length="totalItems"
                    height="calc(100vh - 400px)" fixed-header :loading="loading" @update:options="onOptionsUpdate"
                    :items-per-page="options.itemsPerPage" :page="options.page" hide-default-footer>

                    <template v-slot:item="{ item }">
                        <tr>
                            <td class="checkbox fixed-width">
                                <v-checkbox v-model="selectedCustomers" :value="item.id"></v-checkbox>
                            </td>
                            <td v-for="field in fields" :key="field" class="fixed-width">
                                <template v-if="editCustomerId === item.id">
                                    <v-text-field v-model="editCustomer[field]" :rules="getFieldRules(field)">
                                    </v-text-field>
                                </template>
                                <template v-else>
                                    <a v-if="field === 'id'" :href="`/kunden/kundendetails/${item[field] || ''}`">
                                        {{ item[field] || '' }}
                                    </a>
                                    <span v-else>{{ item[field] || '' }}</span>
                                </template>
                            </td>
                            <td class="table-icon fixed-width">
                                <v-btn icon class="delete-button" variant="plain"
                                    @click="confirmDeleteCustomer(item.id)">
                                    <v-icon>mdi-delete</v-icon>
                                </v-btn>
                            </td>
                            <td class="table-icon fixed-width">
                                <v-btn variant="plain" icon
                                    @click="editCustomerId === item.id ? saveCustomer() : editCustomerDetails(item)">
                                    <v-icon>{{ editCustomerId === item.id ? 'mdi-content-save' : 'mdi-pencil'
                                        }}</v-icon>
                                </v-btn>
                            </td>
                        </tr>
                    </template>
                </v-data-table-server>

                <!-- Pagination Komponente -->
                <div class="pagination-container">
                    <Pagination v-model:page="options.page" v-model:itemsPerPage="options.itemsPerPage"
                        :total-items="totalItems" :items-per-page-options="[10, 20, 50, 100]"
                        @update:page="handlePageChange" @update:itemsPerPage="handleItemsPerPageChange" />
                </div>
            </div>
        </div>

        <VuetifyAlert v-model="isAlertVisible" maxWidth="500" alertTypeClass="alertTypeConfirmation"
            :alertHeading="alertHeading" :alertParagraph="alertParagraph" :alertOkayButton="alertOkayButton"
            alertCloseButton="Abbrechen" @confirmation="handleConfirmation">
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
            customers: [],
            selectedCustomers: [],
            customerToDelete: null,
            isAlertVisible: false,
            headers: [
                { title: 'Auswählen', key: 'checkbox', sortable: false },
                { title: 'ID', key: 'id', sortable: true },
                { title: 'Vorname', key: 'firstname', sortable: true },
                { title: 'Nachname', key: 'lastname', sortable: true },
                { title: 'Email', key: 'email', sortable: true },
                { title: 'Telefonnummer', key: 'phonenumber', sortable: true },
                { title: 'Straße und Hausnummer', key: 'addressline', sortable: true },
                { title: 'PLZ', key: 'postalcode', sortable: true },
                { title: 'Stadt', key: 'city', sortable: true },
                { title: 'Löschen', key: 'delete', sortable: false },
                { title: 'Bearbeiten', key: 'edit', sortable: false }
            ],
            fields: ["id", "firstname", "lastname", "email", "phonenumber", "addressline", "postalcode", "city"],
            editCustomerId: null,
            editCustomer: {},
            loading: false,
            totalItems: 0,
            alertHeading: '',
            alertParagraph: '',
            alertOkayButton: '',
            options: {
                page: 1,
                itemsPerPage: 20,
            },
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
            return this.editCustomerId !== null;
        },
        pageItems() {
            return Array.from({ length: this.totalPages }, (_, i) => i + 1);
        }
    },

    watch: {
        searchString: {
            handler(newValue, oldValue) {
                // Nur reagieren wenn sich der Wert tatsächlich geändert hat
                if (newValue !== oldValue) {
                    this.handleSearchChange(newValue);
                }
            },
            immediate: false
        },
        isSearchActive: {
            handler(newValue) {
                if (!newValue && !this.searchString) {
                    // Suche wurde deaktiviert, zurück zur normalen Ansicht
                    this.options.page = 1;
                    this.loadItems();
                }
            }
        }
    },

    mounted() {
        this.loadItems();
    },

    methods: {
        handleSearchChange(searchValue) {
            // Bestehenden Timer löschen
            if (this.searchDebounceTimer) {
                clearTimeout(this.searchDebounceTimer);
            }

            // Wenn leer, normale Daten laden
            if (!searchValue || !searchValue.trim()) {
                this.options.page = 1;
                this.loadItems();
                return;
            }

            // Debounce für 300ms
            this.searchDebounceTimer = setTimeout(() => {
                this.options.page = 1; // Bei neuer Suche auf Seite 1 zurücksetzen
                this.searchCustomers(searchValue);
            }, 300);
        },

        async searchCustomers(query = this.searchString, page = this.options.page) {
            if (!query || !query.trim()) {
                this.loadItems();
                return;
            }

            this.loading = true;
            try {
                console.log('Searching for:', query, 'Page:', page);

                const params = {
                    query: query.trim(),
                    page: page,
                    itemsPerPage: this.options.itemsPerPage
                };

                const response = await axios.get('/api/customers/search', { params });
                console.log('Search API Response:', response.data);

                // Datenverarbeitung - verschiedene Antwortformate unterstützen
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
                console.error('Fehler beim Suchen der Kunden:', error);
                if (error.response) {
                    console.error('Response data:', error.response.data);
                    console.error('Response status:', error.response.status);
                }

                // Bei Fehler leere Ergebnisse anzeigen
                this.customers = [];
                this.totalItems = 0;
                this.$emit('show-error', 'Fehler beim Durchsuchen der Kunden');
            } finally {
                this.loading = false;
            }
        },

        // Callback für v-data-table-server, wenn sich Optionen ändern
        onOptionsUpdate(newOptions) {
            // Wenn die Seite oder Elemente pro Seite durch die Tabelle geändert wurden
            if (newOptions.page !== this.options.page) {
                this.handlePageChange(newOptions.page);
            }

            if (newOptions.itemsPerPage !== this.options.itemsPerPage) {
                this.handleItemsPerPageChange(newOptions.itemsPerPage);
            }
        },

        handlePageChange(page) {
            this.options.page = page;

            // Entscheiden ob Suche oder normale Daten geladen werden sollen
            if (this.isSearchActive && this.searchString && this.searchString.trim()) {
                this.searchCustomers(this.searchString, page);
            } else {
                this.loadItems();
            }
        },

        handleItemsPerPageChange(itemsPerPage) {
            this.options.itemsPerPage = itemsPerPage;
            this.options.page = 1; // Reset auf Seite 1 wenn sich die Anzahl pro Seite ändert

            if (this.isSearchActive && this.searchString && this.searchString.trim()) {
                this.searchCustomers(this.searchString, 1);
            } else {
                this.loadItems();
            }
        },

        getFieldRules(field) {
            switch (field) {
                case 'id':
                    return [];
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

        showAlert(heading, paragraph, okayButton, action) {
            this.alertHeading = heading;
            this.alertParagraph = paragraph;
            this.alertOkayButton = okayButton;
            this.confirmAction = action;
            this.isAlertVisible = true;
        },

        confirmDeleteCustomer(id) {
            this.customerToDelete = id;
            this.showAlert(
                'Kunde löschen',
                'Möchten Sie den ausgewählten Kunden wirklich löschen?',
                'Löschen',
                this.deleteCustomer
            );
        },

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

        async confirmEditCustomer() {
            try {
                await axios.put(`/api/customers/${this.editCustomerId}`, this.editCustomer);
                this.editCustomerId = null;
                this.editCustomer = {};

                // Nach dem Bearbeiten entsprechende Daten neu laden
                if (this.isSearchActive && this.searchString && this.searchString.trim()) {
                    await this.searchCustomers(this.searchString, this.options.page);
                } else {
                    await this.loadItems();
                }
            } catch (error) {
                console.error('Error data:', error.response?.data);
                this.$emit('show-error', 'Fehler beim Speichern des Kunden');
            }
        },

        handleConfirmation() {
            if (this.confirmAction) {
                this.confirmAction();
            }
            this.isAlertVisible = false;
        },

        async loadItems() {
            this.loading = true;

            try {
                const params = {
                    page: this.options.page,
                    itemsPerPage: this.options.itemsPerPage,
                    orderByNewest: true  // Standardmäßig nach "zuletzt hinzugefügt" sortieren
                };

                console.log('Request params:', params);

                const response = await axios.get('/api/customers', { params });
                console.log('API Response:', response.data);

                this.customers = response.data.items || response.data || [];
                this.totalItems = response.data.total || response.data.totalItems || this.customers.length;
            } catch (error) {
                console.error('Fehler beim Laden der Daten:', error);
                if (error.response) {
                    console.error('Response data:', error.response.data);
                }
                this.customers = [];
                this.totalItems = 0;
            } finally {
                this.loading = false;
            }
        },

        async deleteCustomer() {
            if (this.customerToDelete) {
                try {
                    await axios.delete(`/api/customers/${this.customerToDelete}`);
                    console.log('Customer deleted successfully:', this.customerToDelete);

                    // Prüfen, ob nach dem Löschen die aktuelle Seite noch Elemente hat
                    const isLastItemOnPage = this.customers.length === 1;
                    const isNotFirstPage = this.options.page > 1;

                    // Wenn letztes Element auf einer Seite (nicht der ersten) gelöscht wurde, zur vorherigen Seite wechseln
                    if (isLastItemOnPage && isNotFirstPage) {
                        this.options.page -= 1;
                    }

                    // Nach dem Löschen entsprechende Daten neu laden
                    if (this.isSearchActive && this.searchString && this.searchString.trim()) {
                        await this.searchCustomers(this.searchString, this.options.page);
                    } else {
                        await this.loadItems();
                    }

                    this.selectedCustomers = this.selectedCustomers.filter(id => id !== this.customerToDelete);
                    this.customerToDelete = null;
                } catch (error) {
                    console.error('Fehler beim Löschen des Kunden:', error.response?.data || error.message);
                    this.$emit('show-error', 'Fehler beim Löschen des Kunden');
                }
            }
        },

        async deleteCustomers() {
            if (this.selectedCustomers.length > 0) {
                try {
                    await axios.delete(`/api/customers`, {
                        data: { ids: this.selectedCustomers }
                    });

                    // Prüfen, ob nach dem Löschen die aktuelle Seite noch Elemente hat
                    const itemsBeingDeleted = this.selectedCustomers.length;
                    const remainingItemsOnPage = this.customers.length - itemsBeingDeleted;
                    const isNotFirstPage = this.options.page > 1;

                    // Wenn alle Elemente auf einer Seite (nicht der ersten) gelöscht wurden, zur vorherigen Seite wechseln
                    if (remainingItemsOnPage <= 0 && isNotFirstPage) {
                        this.options.page -= 1;
                    }

                    // Nach dem Löschen entsprechende Daten neu laden
                    if (this.isSearchActive && this.searchString && this.searchString.trim()) {
                        await this.searchCustomers(this.searchString, this.options.page);
                    } else {
                        await this.loadItems();
                    }

                    this.selectedCustomers = [];
                    this.$emit('customersDeleted');
                } catch (error) {
                    console.error('Fehler beim Löschen der Kunden:', error);
                    this.$emit('show-error', 'Fehler beim Löschen der Kunden');
                }
            }
        },

        editCustomerDetails(customer) {
            this.editCustomerId = customer.id;
            this.editCustomer = { ...customer };
        },

        async saveCustomer() {
            try {
                await axios.put(`/api/customers/${this.editCustomerId}`, this.editCustomer);
                this.cancelEdit();

                // Nach dem Speichern entsprechende Daten neu laden
                if (this.isSearchActive && this.searchString && this.searchString.trim()) {
                    await this.searchCustomers(this.searchString, this.options.page);
                } else {
                    await this.loadItems();
                }
            } catch (error) {
                console.error('Fehler beim Speichern des Kunden:', error.response?.data || error.message);
                this.$emit('show-error', 'Fehler beim Speichern des Kunden');
            }
        },

        cancelEdit() {
            this.editCustomerId = null;
            this.editCustomer = {};
        },

        beforeUnmount() {
            if (this.searchDebounceTimer) {
                clearTimeout(this.searchDebounceTimer);
            }
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