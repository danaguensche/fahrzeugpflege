<template>
    <div class="component">
        <div class="header">
            <RefreshButton class="refresh-button" @refresh="loadItems(options)"></RefreshButton>
            <div class="spacer"></div>
            <ButtonGroup class="button-group"></ButtonGroup>
        </div>
        <div class="table-container">
            <div v-if="customers.length === 0"><v-progress-circular indeterminate></v-progress-circular></div>
            <div class="scrollable-table">
                <v-data-table-server :headers="headers" :items="customers" :options.sync="options"
                    :server-items-length="totalItems" :loading="loading" @update:options="loadItems">
                    <template v-slot:item="{ item }">
                        <tr>
                            <td class="checkbox fixed-width">
                                <v-checkbox v-model="selectedCustomers" :value="item.id"></v-checkbox>
                            </td>
                            <template v-for="field in fields" :key="field">
                                <td v-if="editCustomerId === item.id" class="edit-field fixed-width">
                                    <v-text-field v-model="editCustomer[field]"></v-text-field>
                                </td>
                                <td v-else class="fixed-width">{{ item[field] }}</td>
                            </template>
                            <td class="table-icon fixed-width">
                                <v-btn icon class="delete-button" variant="plain" @click="confirmDelete(item.id)">
                                    <v-icon>mdi-delete</v-icon>
                                </v-btn>
                            </td>
                            <td class="table-icon fixed-width">
                                <v-btn variant="plain" icon
                                    @click="editCustomerId === item.id ? saveCustomer(item.id) : editCustomerDetails(item)">
                                    <v-icon>mdi-pencil</v-icon>
                                </v-btn>
                            </td>
                        </tr>
                    </template>
                </v-data-table-server>
            </div>
        </div>
        <VuetifyAlert v-model="isAlertVisible" maxWidth="500" alertTypeClass="alertTypeConfirmation"
            alertHeading="Kunden löschen"
            alertParagraph="Wollen Sie diesen Kunden wirklich löschen? Diese Aktion kann nicht rückgängig gemacht werden."
            alertOkayButton="Löschen" alertCloseButton="Abbrechen" @confirmation="handleConfirmation">
        </VuetifyAlert>
    </div>
</template>

<script>
import ButtonGroup from '../CommonSlots/ButtonGroup.vue';
import RefreshButton from '../CommonSlots/RefreshButton.vue';
import axios from 'axios';
import VuetifyAlert from '../Alerts/VuetifyAlert.vue';

export default {
    name: "CustomerTable",
    components: {
        ButtonGroup,
        RefreshButton,
        VuetifyAlert
    },
    data() {
        return {
            isAlertVisible: false,
            customers: [],
            selectedCustomers: [],
            customerToDelete: null,
            headers: [
                { title: "Auswählen", key: "checkbox", sortable: "false" },
                { title: "Vorname", key: "firstName" },
                { title: "Nachname", key: "lastName" },
                { title: "Email", key: "email" },
                { title: "Telefonnummer", key: "phoneNumber" },
                { title: "Straße und Hausnummer", key: "addressLine" },
                { title: "PLZ", key: "postalCode" },
                { title: "Stadt", key: "city" },
                { title: "Bearbeiten", key: "edit", sortable: "false" },
                { title: "Löschen", key: "delete", sortable: "false" }
            ],
            fields: ["firstName", "lastName", "email", "phoneNumber", "addressLine", "postalCode", "city"],
            editCustomerId: null,
            editCustomer: {},
            loading: false,
            totalItems: 0,
        };
    },

    computed: {
        currentPage() {
            return this.options.page;
        },
        totalPages() {
            return Math.ceil(this.totalItems / this.itemsPerPage);
        },

        options() {
            return {
                page: 1,
                itemsPerPage: 10,
                sortBy: [{ key: 'id' }],
                sortDesc: [false],
            };
        },

        isEditing() {
            return this.editCustomerId !== null;
        },
    },
    methods: {
        showAlert() {
            this.isAlertVisible = true;
        },

        handleConfirmation() {
            this.deleteCustomer();
            this.isAlertVisible = false;
        },
        async loadItems(options) {
            this.loading = true;
            const { page = 1, itemsPerPage = 10, sortBy = [{ key: 'firstName' }], sortDesc = [false] } = options || {};
            try {
                const response = await axios.get('/api/customers', {
                    params: {
                        page,
                        itemsPerPage,
                        sortBy: sortBy.length > 0 ? sortBy[0].key : '',
                        sortDesc: sortDesc.length > 0 ? sortDesc[0] : true,
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
            this.isAlertVisible = true;
        },
        async deleteCustomer() {
            if (this.customerToDelete) {
                try {
                    await axios.delete(`/api/customers/${this.customerToDelete}`);
                    this.customers = this.customers.filter(customer => customer.id !== this.customerToDelete);
                    this.customerToDelete = null;
                    this.isAlertVisible = false;
                } catch (error) {
                    console.error('Fehler beim Löschen des Kunden:', error);
                }
            }
        },
        editCustomerDetails(customer) {
            this.editCustomerId = customer.id;
            this.editCustomer = { ...customer };
        },
        saveCustomer() {
            this.editCustomerId = null;
            this.editCustomer = {};
        },
    }
}
</script>

<style scoped>
.scrollable-table {
    max-height: 800px;
    overflow-y: auto;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.spacer {
    flex-grow: 1;
}

.table-container {
    position: relative;
    width: 100%;
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

@media only screen and (max-width: 600px) {
    .table-container {
        width: 50%;
        height: 50%;
    }

    .custom-table {
        width: 50%;
        height: 50%;
    }
}

@media only screen and (min-height: 1080px) {
    .table-container {
        height: 130%;
    }

    .custom-table {
        height: 130%;
    }
}

@media only screen and (min-height: 1440px) {
    .table-container {
        height: 150%;
    }

    .custom-table {
        height: 150%;
    }
}
</style>