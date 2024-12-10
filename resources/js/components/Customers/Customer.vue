<template>
    <div class="customer-page" :class="{ 'customer-page-sidebar-opened': isSidebarOpen }">
        <Search :context="context" class="searchbar"></Search>
        <div class="content-container">
            <DefaultButton class="addCustomer" @click="addCustomer">Kunde hinzufügen</DefaultButton>
        </div>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Auswählen</th>
                    <th>Firma</th>
                    <th>Vorname</th>
                    <th>Nachname</th>
                    <th>E-Mail</th>
                    <th>Telefonnummer</th>
                    <th>Straße und Hausnummer</th>
                    <th>PLZ</th>
                    <th>Ort</th>
                    <th>Löschen</th>
                    <th>Bearbeiten</th>
                </tr>
            </thead>
            <tbody v-if="this.customers.length > 0">
                <tr v-for="(customer, index) in this.customers" :key="index">
                    <td>
                        <Checkbox></Checkbox>
                    </td>
                    <td>{{ customer.company }}</td>
                    <td>{{ customer.firstname }}</td>
                    <td>{{ customer.lastname }}</td>
                    <td>{{ customer.email }}</td>
                    <td>{{ customer.phonenumber }}</td>
                    <td>{{ customer.addressline }}</td>
                    <td>{{ customer.postalcode }}</td>
                    <td>{{ customer.city }}</td>
                    <td>
                        <DeleteButton></DeleteButton>
                    </td>
                    <td>
                        <EditButton></EditButton>
                    </td>
                </tr>
            </tbody>

            <tbody v-else>
                <tr>
                    <td colspan="11">
                        <div class="loader"></div>
                    </td>
                </tr>
            </tbody>
        </table>
        <CustomerForm v-if="addCustomerValue"></CustomerForm>
    </div>
</template>

<script>
import Checkbox from '../CommonSlots/Checkbox.vue';
import DefaultButton from '../CommonSlots/DefaultButton.vue';
import DeleteButton from '../CommonSlots/DeleteButton.vue';
import EditButton from '../CommonSlots/EditButton.vue';
import Search from '../CommonSlots/Searchbar.vue';
import { mapState } from 'vuex';
import axios from 'axios';
import { RouterLink } from 'vue-router';
import CustomerForm from './addCustomer/CustomerForm.vue';


export default {
    name: 'Customer',
    components: {
        Search,
        DefaultButton,
        Checkbox,
        DeleteButton,
        EditButton,
        CustomerForm
    },

    data() {
        return {
            context: "Suchen Sie nach einem Kunden...",
            customers: [],
            addCustomerValue: false
        }
    },

    props:{

    },

    mounted() {
        console.log("hello?")
        this.getCustomers();
    },

    computed: {
        ...mapState(['isSidebarOpen'])
    },

    methods: {
        addCustomer() {
            this.addCustomerValue = !this.addCustomerValue;
        },

        getCustomers() {
            axios.get('/kunden')
                .then((response) => {
                    this.customers = response.data;
                })
                .catch((error) => {
                    console.error('Fehler beim Abrufen der Kunden:', error);
                });
        }


    }
}
</script>


<style scoped>
.edit-button {
    display: flex;
    align-self: flex-start;
    margin-top: -50px;
    margin-left: -20px;
    transform: scale(0.25);
}

.delete-button {
    display: flex;
    align-self: flex-start;
    margin-top: -50px;
    margin-left: -20px;
    transform: scale(0.25);
}

.table {
    border-collapse: collapse;
    margin: 25px 0;
    font-size: 0.9em;
    font-family: var(--font-family);
    min-width: 400px;
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

.table tbody tr {
    border-bottom: 1px solid #dddddd;
}

.table tbody tr:nth-of-type(even) {
    background-color: #f3f3f3;
}

.table tbody tr:last-of-type {
    border-bottom: 2px solid var(--primary-color);
}



.table tbody tr.active-row {
    font-weight: bold;
    color: #009879;
}

.loader {
    .loader {
        width: 65px;
        display: grid;
        --mask:
            radial-gradient(12px at left 15px top 50%, #0000 95%, #000),
            radial-gradient(12px at right 15px top 50%, #0000 95%, #000);
        -webkit-mask: var(--mask);
        mask: var(--mask);
        -webkit-mask-composite: source-in;
        mask-composite: intersect;
        animation: l1 1s infinite alternate;
    }

    .loader:before,
    .loader:after {
        content: "";
        grid-area: 1/1;
        height: 30px;
        aspect-ratio: 1;
        background: #fff;
        border-radius: 50%;
    }

    .loader:after {
        margin-left: auto;
    }

    @keyframes l1 {
        to {
            width: 40px;
        }
    }
}


.table-bordered {}

.customer-page {
    display: flex;
    flex-direction: column;
    margin-left: 150px;
    margin-right: 30px;
    transition: margin-left 0.3s ease;
    font-family: var(--font-family);
}

.customer-page-sidebar-opened {
    margin-left: 350px;
}



.content-container {
    /* Abstand Formular und Searchbar */
    margin-top: -110px;
    display: flex;
    justify-content: flex-end;
}

.form-container {
    width: 50%;
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