<template>
    <div class="customer-page" :class="{ 'customer-page-sidebar-opened': isSidebarOpen }">

        <Search :context="context" class="searchbar"></Search>

        <div class="content-container">
            <DefaultButton class="addCustomer" @click="addCustomer">Kunde hinzufügen</DefaultButton>
        </div>

        <div class="form-container">
            <CustomerForm :isOpen="showCustomerForm" @close="showCustomerForm = false"></CustomerForm>
        </div>

        <CustomerTable :customers="customers" :editCustomerId="editCustomerId" :editCustomer="editCustomer"
            :currentPage="currentPage" :totalPages="totalPages" @edit-customer="editCustomerDetails"
            @save-customer="saveCustomer" @page-changed="changePage" @update:option="handleSortChange"></CustomerTable>
    </div>
</template>

<script>
import DefaultButton from '../CommonSlots/DefaultButton.vue';
import Search from '../CommonSlots/Searchbar.vue';
import { mapState } from 'vuex';
import axios from 'axios';
import CustomerForm from './addCustomer/CustomerForm.vue';
import CustomerTable from './CustomerTable.vue';


export default {
    name: 'Customer',
    components: {
        Search,
        DefaultButton,
        CustomerForm,
        CustomerTable
    },

    data() {
        return {
            context: "Suchen Sie nach einem Kunden...",
            customers: [],
            showCustomerForm: false,
            editCustomerId: null,
            editCustomer: {},
            currentPage: 1,
            totalPages: 1,
        }
    },

    mounted() {
        this.getCustomers();
    },

    computed: {
        ...mapState(['isSidebarOpen'])
    },

    methods: {
        addCustomer() {
            this.showCustomerForm = !this.showCustomerForm;
        },

        changePage(page) {
            this.getCustomers(page);
        },

        handleSortChange({ sortBy, sortDesc }) {
            this.getCars(this.currentPage, sortBy[0], sortDesc[0]);
        },

        editCustomerDetails(customer) {
            this.editCustomerId = customer.id;
            this.editCustomer = { ...customer };
        },


        getCustomers(page = 1, sortBy = 'id', sortDesc = false) {
            axios.get('/api/customers', {
                params: {
                    page: page,
                    per_page: 10,
                    sortBy: sortBy,
                    sortDesc: sortDesc
                }
            })
                .then((response) => {
                    this.cs = response.data.items;
                    this.totalPages = Math.ceil(response.data.totalItems / 20);
                    this.currentPage = page;
                })
                .catch((error) => {
                    console.error('Fehler beim Abrufen der Kunden:', error);
                });
        },

        saveCustomer(id) {
            axios.put(`/api/customers/${id}`, this.editCustomer)
                .then(() => {
                    this.getCustomers(this.currentPage);
                    this.editCustomerId = null;
                    this.editCustomer = {};
                })
                .catch((error) => {
                    console.error('Fehler beim Speichern des Kunden:', error);
                });
        }
    }
}
</script>

<style scoped>
.customer-page {
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
    /* Abstand Formular und Searchbar */
    margin-top: -110px;
    display: flex;
    justify-content: flex-end;
}

.form-container {
    margin-top: 1vh;
    margin-bottom: 1vh;
}

.customer-page-sidebar-opened {
    margin-left: 330px;
    transition: margin-left 0.3s ease;
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

.select {
    width: 0.5vh;
}

.checkbox {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100%;
}

.table-container {
    width: 100%;
}

.table-container-sidebar-opened {
    width: 100%;
}

.table-icon {
    width: 0.5vh;
}

.table {
    border-collapse: separate;
    margin: 25px 0;
    font-size: 0.9em;
    font-family: var(--font-family);
    width: 92%;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
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

.pagination-container {
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 20px auto;
}

.pagination {
    display: inline-block;
}

.pagination p {
    color: black;
    float: left;
    padding: 8px 16px;
    text-decoration: none;
}

.pagination p.active {
    background-color: var(--primary-color);
    color: var(--background-color);
}

.pagination p:hover {
    cursor: pointer;
    background-color: #ddd;
}

@media only screen and (max-width: 650px) {
    .customer-page {
        margin-left: 160px;
        font-size: 12px;
    }

    .customer-page-sidebar-opened {
        margin-left: 260px;
    }

    .content-container {
        flex-direction: column;
    }

    .form-container {
        width: 100%;
    }

    .searchbar {
        width: 300px;
    }
}
</style>