<template>
    <div class="car-page" :class="{ 'car-page-sidebar-opened': isSidebarOpen }">
        <Search :context="context" class="searchbar"></Search>

        <div class="content-container">
            <DefaultButton class="addCar" @click="addCar">Fahrzeug hinzufügen</DefaultButton>
        </div>

        <div class="form-container">
            <CarForm :isOpen="showCarForm" @close="showCarForm = false"></CarForm>
        </div>

        <CarTable
            :cars="cars"
            :editCarId="editCarId"
            :editCar="editCar"
            :currentPage="currentPage"
            :totalPages="totalPages"
            @edit-car="editCarDetails"
            @save-car="saveCar"
            @page-changed="changePage"
        ></CarTable>
    </div>
</template>

<script>
import axios from 'axios';
import { mapState } from 'vuex';
import Search from '../CommonSlots/Searchbar.vue';
import DefaultButton from '../CommonSlots/DefaultButton.vue';
import CarForm from './addCar/CarForm.vue';
import CarTable from './CarTable.vue';

export default {
    components: {
        Search,
        DefaultButton,
        CarForm,
        CarTable
    },
    data() {
        return {
            context: "Suchen Sie nach einem Fahrzeug...",
            cars: [],
            showCarForm: false,
            editCarId: null,
            editCar: {},
            currentPage: 1,
            totalPages: 1,
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
        changePage(page) {
            this.getCars(page);
        },
        getCars(page = 1) {
            axios.get(`/api/fahrzeuge?page=${page}`)
                .then((response) => {
                    this.cars = response.data.data;
                    this.totalPages = response.data.meta.last_page;
                    this.currentPage = page;
                })
                .catch((error) => {
                    console.error('Fehler beim Abrufen der Fahrzeuge:', error);
                });
        },
        editCarDetails(car) {
            this.editCarId = car.Kennzeichen;
            this.editCar = { ...car };
        },
        saveCar(kennzeichen) {
            axios.put(`/api/fahrzeuge/${kennzeichen}`, this.editCar)
                .then(() => {
                    this.getCars(this.currentPage);
                    this.editCarId = null;
                    this.editCar = {};
                })
                .catch((error) => {
                    console.error('Fehler beim Speichern des Autos:', error);
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
    margin-top: 1vh;
    margin-bottom: 1vh;
}

.car-page-sidebar-opened {
    margin-left: 330px;
    transition: margin-left 0.3s ease;
}

.select {
    width: 0.5vh;
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
