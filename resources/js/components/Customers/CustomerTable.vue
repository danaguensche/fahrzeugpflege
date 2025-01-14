<template>
    <div class="table-container" :class="{ 'table-container-sidebar-opened': isSidebarOpen }">
        <div v-if="customers.length === 0">Laden...</div>
        <table v-else class="table table-bordered">
            <thead>
                <tr>
                    <th class="select">Auswählen</th>
                    <th>Firma</th>
                    <th>Vorname</th>
                    <th>Nachname</th>
                    <th>Email</th>
                    <th>Telefonnummer</th>
                    <th>Straße und Hausnummer</th>
                    <th>PLZ</th>
                    <th>Stadt</th>
                    <th>Bearbeiten</th>
                    <th>Löschen</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="customer in customers" :key="customer.id">
                    <td class="checkbox">
                        <Checkbox></Checkbox>
                    </td>
                    <td v-if="editCustomerId === customer.id" class="edit-field">
                        <input v-model="editCustomer.company" />
                    </td>
                    <td v-else>
                        {{ customer.company }}
                    </td>
                    <td v-if="editCustomerId === customer.id" class="edit-field">
                        <input v-model="editCustomer.firstName" />
                    </td>
                    <td v-else>{{ customer.firstName }}</td>
                    <td v-if="editCustomerId === customer.id" class="edit-field">
                        <input v-model="editCustomer.lastName" />
                    </td>
                    <td v-else>{{ customer.lastName }}</td>
                    <td v-if="editCustomerId === customer.id" class="edit-field">
                        <input v-model="editCustomer.email" />
                    </td>
                    <td v-else>{{ customer.email }}</td>
                    <td v-if="editCustomerId === customer.id" class="edit-field">
                        <input v-model="editCustomer.phoneNumber" />
                    </td>
                    <td v-else>{{ customer.phoneNumber }}</td>
                    <td v-if="editCustomerId === customer.id" class="edit-field">
                        <input v-model="editCustomer.addressLine" />
                    </td>
                    <td v-else>{{ customer.addressLine }}</td>
                    <td v-if="editCustomerId === customer.id" class="edit-field">
                        <input v-model="editCustomer.postalCode" />
                    </td>
                    <td v-else>{{ customer.postalCode }}</td>
                    <td v-if="editCustomerId === customer.id" class="edit-field">
                        <input v-model="editCustomer.city" />
                    </td>
                    <td v-else>{{ customer.city }}</td>
                    <td class="table-icon">
                        <DeleteButton></DeleteButton>
                    </td>
                    <td class="table-icon">
                        <EditButton
                            @click="editCustomerId === customer.id ? saveCustomer(customer.id) : editCustomerDetails(customer)" />
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <Pagination :currentPage="currentPage" :totalPages="totalPages" @page-changed="changePage"></Pagination>
</template>

<script>
import Checkbox from '../CommonSlots/Checkbox.vue';
import DeleteButton from '../CommonSlots/DeleteButton.vue';
import EditButton from '../CommonSlots/EditButton.vue';
import Pagination from '../CommonSlots/Pagination.vue';

export default {
    name: "CustomerTable",
    components: {
        Checkbox,
        DeleteButton,
        EditButton,
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
    methods: {
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
.edit-field {
    padding: 10px 12px;
    width: 100%;
}

.edit-field input {
    width: 100%;
    padding: 8px;
    font-size: 1em;
    box-sizing: border-box;
}

.checkbox {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100%;
}

.edit-button {
    display: flex;
    margin-top: -50px;
    justify-content: center;
    align-content: center;
    transform: scale(0.25);
    margin-left: -1.5vh;
}

.delete-button {
    display: flex;
    margin-top: -50px;
    justify-content: center;
    align-content: center;
    transform: scale(0.25);
    margin-left: -1.5vh;
}

.table-container {
    width: 100%;
}

.table-container-sidebar-opened {
    width: 100%;
}

.table {
    border-collapse: separate;
    margin: 25px 0;
    font-size: 0.9em;
    font-family: var(--font-family);
    width: 92%;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
}

.table-icon {
    width: 0.5vh;
}

.table thead tr {
    background-color: var(--primary-color);
    color: var(--text-color-light);
    text-align: left;
}

.table th,
.table td {
    padding: 12px 15px;
}

.table th {
    height: 45px;
}

.table tbody tr {
    border-bottom: 1px solid #dddddd;
}

.table tbody tr:nth-of-type(even) {
    background-color: #f3f3f3;
}

.table tbody tr.active-row {
    font-weight: bold;
    color: #009879;
}
</style>