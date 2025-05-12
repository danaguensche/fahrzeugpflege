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
                    @click="confirmDeleteCustomers">
                    Löschen
                </DeleteButton>
            </div>
        </div>

        <div class="table-container">
            <div v-if="loading" class="loading-indicator">
                <v-progress-circular indeterminate></v-progress-circular>
            </div>
            <div class="scrollable-table">
                <v-data-table-server 
                    :headers="headers" 
                    :items="customers" 
                    :server-items-length="totalItems"
                    height="calc(100vh - 450px)" 
                    fixed-header 
                    :loading="loading" 
                    @update:options="onUpdateOptions" 
                    single-sort
                    item-key="id" 
                    :sort-by="sortBy"
                    :sort-desc="sortDesc"
                    :items-per-page="options.itemsPerPage" 
                    hide-default-footer
                    class="customer-table">

                    <template v-slot:item="{ item }">
                        <tr :class="{ 'edited-row': editCustomerId === item.id }">
                            <td class="checkbox fixed-width">
                                <v-checkbox v-model="selectedCustomers" :value="item.id"></v-checkbox>
                            </td>
                            <td v-for="field in fields" :key="field" class="fixed-width">
                                <template v-if="editCustomerId === item.id">
                                    <template v-if="field === 'id'">
                                        <a :href="`/kunden/kundendetails/${editCustomer[field] || ''}`">
                                            {{ editCustomer[field] || '' }}
                                        </a>
                                    </template>
                                    <template v-else>
                                        <v-text-field v-model="editCustomer[field]" :rules="getFieldRules(field)"
                                            :error-messages="fieldErrors[field]" density="compact"></v-text-field>
                                    </template>
                                </template>
                                <template v-else>
                                    <a v-if="field === 'id'" :href="`/kunden/kundendetails/${item[field] || ''}`">
                                        {{ item[field] || '' }}
                                    </a>
                                    <span v-else>{{ item[field] || '' }}</span>
                                </template>
                            </td>
                            <td class="table-icon fixed-width">
                                <v-btn icon class="delete-button" variant="plain" @click="confirmDelete(item.id)">
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
                <Pagination v-model:page="options.page" v-model:itemsPerPage="options.itemsPerPage"
                    :total-items="totalItems" :items-per-page-options="[10, 20, 50, 100]"
                    @update:page="handlePageChange" @update:itemsPerPage="handleItemsPerPageChange" />
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
    components: {
        RefreshButton,
        VuetifyAlert,
        ConfirmButton,
        CancelButton,
        DeleteButton,
        Pagination
    },
    data() {
        return {
            isAlertVisible: false,
            customers: [],
            selectedCustomers: [],
            customerToDelete: null,
            headers: [
                { title: "Auswählen", key: "checkbox", sortable: false, width: '80px' },
                { title: "ID", key: "id", sortable: true, align: 'start' },
                { title: "Vorname", key: "firstname", sortable: true },
                { title: "Nachname", key: "lastname", sortable: true },
                { title: "Email", key: "email", sortable: true },
                { title: "Telefonnummer", key: "phonenumber", sortable: true },
                { title: "Straße und Hausnummer", key: "addressline", sortable: true },
                { title: "PLZ", key: "postalcode", sortable: true },
                { title: "Stadt", key: "city", sortable: true },
                { title: "Löschen", key: "delete", sortable: false, width: '60px' },
                { title: "Bearbeiten", key: "edit", sortable: false, width: '60px' }
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
                sortBy: [],
                sortDesc: [],
            },
            sortBy: [],
            sortDesc: [],
            confirmAction: null,
            fieldErrors: {}
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
        }
    },

    mounted() {
        this.loadItems();
    },

    methods: {
        isEditableField(field) {
            return field !== 'id' && field !== 'checkbox' && field !== 'edit' && field !== 'delete';
        },

        handlePageChange(page) {
            this.options.page = page;
            this.loadItems();
        },

        handleItemsPerPageChange(itemsPerPage) {
            this.options.itemsPerPage = itemsPerPage;
            this.options.page = 1; // Reset auf Seite 1 wenn sich die Anzahl pro Seite ändert
            this.loadItems();
        },
        
        onUpdateOptions(newOptions) {
            // Extrahieren der Sortierinformationen
            const sortBy = Array.isArray(newOptions.sortBy) && newOptions.sortBy.length ? [newOptions.sortBy[0]] : [];
            const sortDesc = Array.isArray(newOptions.sortDesc) && newOptions.sortDesc.length ? [newOptions.sortDesc[0]] : [];
            
            // Für die Anzeige in der Tabelle
            this.sortBy = sortBy;
            this.sortDesc = sortDesc;
            
            // Für die API-Anfrage
            this.options = {
                page: newOptions.page || this.options.page,
                itemsPerPage: newOptions.itemsPerPage || this.options.itemsPerPage,
                sortBy: sortBy,
                sortDesc: sortDesc
            };
            
            // Daten neu laden
            this.loadItems();
        },

        getFieldRules(field) {
            switch (field) {
                case 'id':
                    return [];
                case 'firstName':
                    return [value => !!value || 'Vorname ist erforderlich'];
                case 'lastName':
                    return [value => !!value || 'Nachname ist erforderlich'];
                case 'email':
                    return [
                        value => !!value || 'Email ist erforderlich',
                        value => /.+@.+\..+/.test(value) || 'Email muss gültig sein'
                    ];
                case 'phoneNumber':
                    return [value => !!value || 'Telefonnummer ist erforderlich'];
                case 'addressLine':
                    return [value => !!value || 'Adresse ist erforderlich'];
                case 'postalCode':
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
                    orderByNewest: true
                };

                if (this.options.sortBy && this.options.sortBy.length > 0) {
                    params.sortBy = this.options.sortBy[0];
                    params.sortDesc = this.options.sortDesc[0] ? 'true' : 'false';
                    params.orderByNewest = false;
                }

                const response = await axios.get('/api/customers', { params });
                this.customers = response.data.items;
                this.totalItems = response.data.total;
            } catch (error) {
                console.error('Fehler beim Laden der Daten:', error);
                if (error.response) {
                    console.error('Response data:', error.response.data);
                }
            } finally {
                this.loading = false;
            }
        },

        confirmDelete(id) {
            this.customerToDelete = id;
            this.showAlert(
                'Kunde löschen',
                'Möchten Sie den ausgewählten Kunden wirklich löschen?',
                'Löschen',
                this.deleteCustomer
            );
        },

        confirmDeleteCustomers() {
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

        confirmEditCustomer() {
            if (this.editCustomerId) {
                this.showAlert(
                    'Kunde bearbeiten',
                    'Möchten Sie die Änderungen wirklich speichern?',
                    'Speichern',
                    this.updateCustomer
                );
            }
        },

        async deleteCustomer() {
            if (this.customerToDelete) {
                try {
                    await axios.delete(`/api/customers/${this.customerToDelete}`);
                    await this.loadItems();
                    this.selectedCustomers = this.selectedCustomers.filter(id => id !== this.customerToDelete);
                    this.customerToDelete = null;
                } catch (error) {
                    console.error('Fehler beim Löschen des Kunden:', error.response?.data || error.message);
                }
            }
        },

        async deleteCustomers() {
            if (this.selectedCustomers.length > 0) {
                try {
                    await axios.delete(`/api/customers`, {
                        data: { ids: this.selectedCustomers }
                    });
                    await this.loadItems();
                    this.selectedCustomers = [];
                    this.$emit('customersDeleted');
                } catch (error) {
                    console.error('Fehler beim Löschen der Kunden:', error);
                }
            }
        },

        editCustomerDetails(customer) {
            this.editCustomerId = customer.id;
            this.editCustomer = { ...customer };
        },

        async updateCustomer() {
            try {
                const formattedData = {
                    firstname: this.editCustomer.firstName,
                    lastName: this.editCustomer.lastName,
                    email: this.editCustomer.email,
                    phoneNumber: this.editCustomer.phoneNumber,
                    addressLine: this.editCustomer.addressLine,
                    postalCode: this.editCustomer.postalCode,
                    city: this.editCustomer.city,
                    company: this.editCustomer.company
                };

                await axios.put(`/api/customers/${this.editCustomerId}`, formattedData);
                this.cancelEdit();
                await this.loadItems();
            } catch (error) {
                console.error('Fehler beim Aktualisieren des Kunden:', error.response?.data?.error || error.message);
            }
        },

        saveCustomer() {
            this.confirmEditCustomer();
        },

        cancelEdit() {
            this.editCustomerId = null;
            this.editCustomer = {};
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