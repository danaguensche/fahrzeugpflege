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
        <alert-base v-if="alertVisible" v-on="alertHandlers" alert-heading="Wollen Sie diesen Kunden wirklich löschen?"
            alert-close-button="Abbrechen" alert-okay-button="Löschen" alert-type-class="alertTypeConfirmation">
        </alert-base>
    </div>
</template>

<script>
import ButtonGroup from '../CommonSlots/ButtonGroup.vue';
import RefreshButton from '../CommonSlots/RefreshButton.vue';
import AlertBase from '../Alerts/AlertBase.vue';
import axios from 'axios';

export default {
    name: "CustomerTable",
    components: {
        ButtonGroup,
        RefreshButton,
        AlertBase
    },
    data() {
        return {
            customers: [],
            selectedCustomers: [],
            showDeleteButton: false,
            customerToDelete: null,
            alertVisible: false,
            alertHandlers: {
                confirmationClicked: this.deleteCustomer,
                closeAlertClicked: this.updateAlertVisibility
            },
            headers: [
                { title: "Auswählen", key: "checkox", sortable: "false" },
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
            fields: ["firstName", "lastName", "email", "phoneNumber", "addressLine", "postalCode", "city"]
        };
    },
    methods: {
        async loadItems(options) {
            this.loading = true;
            const { page = 1, itemsPerPage = 10, sortBy = [{ key: 'firstName' }], sortDesc = [false] } = options || {};
            try {
                const response = await axios.get('/api/customers', {
                    params: {
                        page,
                        itemsPerPage,
                        sortBy: sortBy.length > 0 ? sortBy[0].key : '',
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
            this.alertVisible = true;
        },
        deleteCustomer() {
            if (this.customerToDelete) {
                console.log('Deleting customer:', this.customerToDelete);
                this.customerToDelete = null;
                this.alertVisible = false;
            }
        },
        editCustomerDetails(customer) {
            this.editCustomerId = customer.id;
            this.editCustomer = { ...customer };
        },
        saveCustomer(id) {
            this.editCustomerId = null;
            this.editCustomer = {};
        },
        updateAlertVisibility() {
            this.alertVisible = !this.alertVisible;
        }
    }
}
</script>

<style scoped>
.scrollable-table {
    max-height: 800px; /* Setzen Sie die gewünschte Höhe */
    overflow-y: auto;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    /* margin-bottom: 10px; */
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