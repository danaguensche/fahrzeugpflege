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
            <div v-if="loading"><v-progress-circular indeterminate></v-progress-circular></div>
            <div class="scrollable-table">
                <v-data-table-server :headers="headers" :items="customers" :options.sync="options"
                    :server-items-length="totalItems" :loading="loading" @update:options="loadItems" height="800">
                    <template v-slot:item="{ item }">
                        <tr>
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
                                            :error-messages="fieldErrors[field]" dense outlined
                                            hide-details="auto"></v-text-field>
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

export default {
    name: "CustomerTable",
    components: {
        RefreshButton,
        VuetifyAlert,
        ConfirmButton,
        CancelButton,
        DeleteButton
    },
    data() {
        return {
            isAlertVisible: false,
            customers: [],
            selectedCustomers: [],
            customerToDelete: null,
            headers: [
                { title: "Auswählen", key: "checkbox", sortable: false },
                { title: "Kundennummer", key: "id", sortable: true },
                { title: "Vorname", key: "firstName" },
                { title: "Nachname", key: "lastName" },
                { title: "Email", key: "email" },
                { title: "Telefonnummer", key: "phoneNumber" },
                { title: "Straße und Hausnummer", key: "addressLine" },
                { title: "PLZ", key: "postalCode" },
                { title: "Stadt", key: "city" },
                { title: "Bearbeiten", key: "edit", sortable: false },
                { title: "Löschen", key: "delete", sortable: false }
            ],
            fields: ["id", "firstName", "lastName", "email", "phoneNumber", "addressLine", "postalCode", "city"],
            editCustomerId: null,
            editCustomer: {},
            loading: false,
            totalItems: 0,
            alertHeading: '',
            alertParagraph: '',
            alertOkayButton: '',
            options: {
                page: 1,
                itemsPerPage: 10,
                sortBy: [{ key: 'id' }],
                sortDesc: [false],
            },
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
        },
    },

    mounted() {
        this.loadItems();
    },

    methods: {
        isEditableField(field) {
            return field !== 'id' && field !== 'checkbox' && field !== 'edit' && field !== 'delete';
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
            const { page, itemsPerPage, sortBy, sortDesc } = this.options;
            try {
                const response = await axios.get('/api/customers', {
                    params: {
                        page,
                        itemsPerPage,
                        sortBy: sortBy.length > 0 ? sortBy[0].key : 'id',
                        sortDesc: sortDesc.length > 0 ? sortDesc[0] : false,
                    },
                });
                this.customers = response.data.items;
                this.totalItems = response.data.total;
            } catch (error) {
                console.error('Fehler beim Laden der Daten:', error);
            }
            this.loading = false;
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

                this.showAlert(
                    'Kunden löschen',
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
                    this.customers = this.customers.filter(customer => customer.id !== this.customerToDelete);
                    this.customerToDelete = null;

                    this.selectedCustomers = this.selectedCustomers.filter(id => id !== this.customerToDelete);
                } catch (error) {
                    console.error('Fehler beim Löschen des Kunden:', error);
                }
            }
        },

        async deleteCustomers() {
            if (this.selectedCustomers.length > 0) {
                try {
                    await axios.delete(`/api/customers`, {
                        data: { ids: this.selectedCustomers }
                    });
                    this.customers = this.customers.filter(customer => !this.selectedCustomers.includes(customer.id));
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
            delete this.editCustomer.id;
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
                alert('Fehler beim Aktualisieren des Kunden: ' + (error.response?.data?.error || error.message));
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
    align-items: flex-start;
    margin-bottom: 20px;
    flex-direction: row;
}

.scrollable-table {
    min-height: 900px !important;
    overflow-y: auto;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0px;
}

.spacer {
    flex-grow: 1;
}

.table-container {
    position: relative;
    width: 1500px;
}

.custom-table {
    width: 100%;
}

.fixed-width {
    width: 150px;
}

.edit-field {
    padding: 10px 12px;
}

.edit-field input {
    padding: 8px;
    font-size: 1em;
    box-sizing: border-box;
    width: 100%;
}

.edit-button,
.delete-button {
    display: flex;
    justify-content: center;
    align-items: center;
}

.delete-button:hover {
    background-color: red !important;
}

.table {
    margin: 25px 0;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
}

.table-icon {
    width: 0.5vh;
}
</style>