<template>
    <div class="component">
        <div class="table-container">
            <div v-if="customers.length === 0">Laden...</div>
            <v-table v-else class="custom-table" fixed-header height="850">
                <thead>
                    <tr>
                        <th class="select fixed-width">Auswählen</th>
                        <!-- <th class="fixed-width">Firma</th> -->
                        <th class="fixed-width">Vorname</th>
                        <th class="fixed-width">Nachname</th>
                        <th class="fixed-width">Email</th>
                        <th class="fixed-width">Telefonnummer</th>
                        <th class="fixed-width">Straße und Hausnummer</th>
                        <th class="fixed-width">PLZ</th>
                        <th class="fixed-width">Stadt</th>
                        <th class="fixed-width">Bearbeiten</th>
                        <th class="fixed-width">Löschen</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="customer in customers" :key="customer.id">
                        <td class="checkbox fixed-width">
                            <v-checkbox v-model="selectedCustomers" :value="customer.id"></v-checkbox>
                        </td>
                        <!-- <td v-if="editCustomerId === customer.id" class="edit-field fixed-width"> -->
                            <!-- <v-text-field v-model="editCustomer.company"></v-text-field>
                        </td>
                        <td v-else class="fixed-width">{{ customer.company }}</td> -->
                        <td v-if="editCustomerId === customer.id" class="edit-field fixed-width">
                            <v-text-field v-model="editCustomer.firstName"></v-text-field>
                        </td>
                        <td v-else class="fixed-width">{{ customer.firstName }}</td>
                        <td v-if="editCustomerId === customer.id" class="edit-field fixed-width">
                            <v-text-field v-model="editCustomer.lastName"></v-text-field>
                        </td>
                        <td v-else class="fixed-width">{{ customer.lastName }}</td>
                        <td v-if="editCustomerId === customer.id" class="edit-field fixed-width">
                            <v-text-field v-model="editCustomer.email"></v-text-field>
                        </td>
                        <td v-else class="fixed-width">{{ customer.email }}</td>
                        <td v-if="editCustomerId === customer.id" class="edit-field fixed-width">
                            <v-text-field v-model="editCustomer.phoneNumber"></v-text-field>
                        </td>
                        <td v-else class="fixed-width">{{ customer.phoneNumber }}</td>
                        <td v-if="editCustomerId === customer.id" class="edit-field fixed-width">
                            <v-text-field v-model="editCustomer.addressLine"></v-text-field>
                        </td>
                        <td v-else class="fixed-width">{{ customer.addressLine }}</td>
                        <td v-if="editCustomerId === customer.id" class="edit-field fixed-width">
                            <v-text-field v-model="editCustomer.postalCode"></v-text-field>
                        </td>
                        <td v-else class="fixed-width">{{ customer.postalCode }}</td>
                        <td v-if="editCustomerId === customer.id" class="edit-field fixed-width">
                            <v-text-field v-model="editCustomer.city"></v-text-field>
                        </td>
                        <td v-else class="fixed-width">{{ customer.city }}</td>
                        <td class="table-icon fixed-width">
                            <v-btn icon class="delete-button" variant="plain">
                                <v-icon>mdi-delete</v-icon>
                            </v-btn>
                        </td>
                        <td class="table-icon fixed-width">
                            <v-btn variant="plain" icon
                                @click="editCustomerId === customer.id ? saveCustomer(customer.id) : editCustomerDetails(customer)">
                                <v-icon>mdi-pencil</v-icon>
                            </v-btn>
                        </td>
                    </tr>
                </tbody>
            </v-table>
        </div>
        <Pagination :currentPage="currentPage" :totalPages="totalPages" @page-changed="changePage"></Pagination>
    </div>
</template>

<script>
import Checkbox from '../CommonSlots/Checkbox.vue';
import Pagination from '../CommonSlots/Pagination.vue';

export default {
    name: "CustomerTable",
    components: {
        Checkbox,
        Pagination
    },
    props: {
        customers: {
            type: Array,
            required: true
        },
        editCustomerId: {
            type: String,
            default: null
        },
        editCustomer: {
            type: Object,
            default: () => ({})
        },
        currentPage: {
            type: Number,
            required: true
        },
        totalPages: {
            type: Number,
            required: true
        }
    },
    data() {
        return {
            selectedCustomers: [],
            showDeleteButton: false
        };
    },
    methods: {
        updateSelectedCustomers(customerId, isChecked) {
            if (isChecked) {
                this.selectedCustomers.push(customerId);
            } else {
                this.selectedCustomers = this.selectedCustomers.filter(id => id !== customerId);
            }
        },
        deleteCustomers() {
            console.log('Deleting customers:', this.selectedCustomers);

        },
        deleteCustomer(customerId) {
            console.log('Deleting customer:', customerId);
        },
        editCustomerDetails(customer) {
            this.$emit('edit-customer', customer);
        },
        saveCustomer(customerId) {
            this.$emit('save-customer', customerId);
        },
        changePage(page) {
            this.$emit('page-changed', page);
        }
    }
}
</script>

<style scoped>
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