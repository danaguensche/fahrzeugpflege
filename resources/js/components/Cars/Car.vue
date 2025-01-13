<template>
    <div class="car-page" :class="{ 'car-page-sidebar-opened': isSidebarOpen }">
        <Search :context="context" class="searchbar"></Search>

        <div class="content-container">
            <DefaultButton class="addCar" @click="addCar">Fahrzeug hinzufügen</DefaultButton>
        </div>


        <div class="form-container">
            <CarForm :isOpen="showCarForm" @close="showCarForm = false"></CarForm>
        </div>



        <div class="table-container" :class="{ 'table-container-sidebar-opened': isSidebarOpen }">
            <div v-if="cars.length === 0">Laden...</div>
            <table v-else class="table table-bordered">
                <thead>
                    <tr>
                        <!-- <th>Auswählen</th> -->
                        <th>Kennzeichen</th>
                        <th>Fahrzeugklasse</th>
                        <th>Automarke</th>
                        <th>Typ</th>
                        <th>Farbe</th>
                        <th>Löschen</th>
                        <th>Bearbeiten</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="car in cars" :key="car.Kennzeichen">
                        <!-- <td>
                            <Checkbox></Checkbox>
                        </td> -->
                        <td><a href="">{{ car.Kennzeichen }}</a></td>
                        <td>{{ car.Fahrzeugklasse }}</td>
                        <td>{{ car.Automarke }}</td>
                        <td>{{ car.Typ }}</td>
                        <td>{{ car.Farbe }}</td>
                        <td>
                            <DeleteButton></DeleteButton>
                        </td>
                        <td>
                            <EditButton></EditButton>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import Checkbox from '../CommonSlots/Checkbox.vue';
import DefaultButton from '../CommonSlots/DefaultButton.vue';
import DeleteButton from '../CommonSlots/DeleteButton.vue';
import EditButton from '../CommonSlots/EditButton.vue';
import Search from '../CommonSlots/Searchbar.vue';
import axios from 'axios';
import CarForm from './addCar/CarForm.vue';

import { mapState } from 'vuex';

export default {
    name: "CarPage",

    components: {
        CarForm,
        Search,
        DeleteButton,
        DefaultButton,
        EditButton,
        Checkbox

    },
    data() {
        return {
            context: "Suchen Sie nach einem Fahrzeug...",
            cars: [],
            showCarForm: false,
        }
    },

    mounted() {
        this.getCars();
    },

    computed: {
        ...mapState(['isSidebarOpen'])
    },

    methods: {
        addCar() {
            this.showCarForm = !this.showCarForm;
        },

        getCars() {
            axios.get('/fahrzeuge')
                .then(response => {
                    console.log('Antwort erhalten:', response.data);
                    this.cars = response.data.data;
                })
                .catch(error => {
                    console.error('Fehler beim Abrufen der Fahrzeuge:', error);
                });
        }
    }
}

</script>

<style scoped>
.car-page {
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
    margin-top: 2vh;
    margin-bottom: 1vh;
}


.car-page-sidebar-opened {
    margin-left: 330px;
    transition: margin-left 0.3s ease;
}

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

.table-container {
    width: 130vh;
}

.table-container-sidebar-opened {
    width: 115vh;
}

.table {
    border-collapse: collapse;
    margin: 25px 0;
    font-size: 0.9em;
    font-family: var(--font-family);
    width: 100%;
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
