<template>
    <div>
        <div class="table-container">
            <div v-if="cars.length === 0">Laden...</div>
            <v-table v-else class="custom-table" fixed-header height="850">
                <thead>
                    <tr>
                        <th class="select ">Auswählen</th>
                        <th class="fixed-width">Kennzeichen</th>
                        <th class="fixed-width">Fahrzeugklasse</th>
                        <th class="fixed-width">Automarke</th>
                        <th class="fixed-width">Typ</th>
                        <th class="fixed-width">Farbe</th>
                        <th class="fixed-width">Löschen</th>
                        <th class="fixed-width">Bearbeiten</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="car in cars" :key="car.Kennzeichen">
                        <td class="checkbox fixed-width">
                            <v-checkbox v-model="selectedCars" :value="car.Kennzeichen"></v-checkbox>
                        </td>
                        <td v-if="editCarId === car.Kennzeichen" class="edit-field fixed-width">
                            <v-text-field v-model="editCar.Kennzeichen"></v-text-field>
                        </td>
                        <td v-else class="fixed-width">
                            <a href="">{{ car.Kennzeichen }}</a>
                        </td>
                        <td v-if="editCarId === car.Kennzeichen" class="edit-field fixed-width">
                            <v-text-field v-model="editCar.Fahrzeugklasse"></v-text-field>
                        </td>
                        <td v-else class="fixed-width">{{ car.Fahrzeugklasse }}</td>
                        <td v-if="editCarId === car.Kennzeichen" class="edit-field fixed-width">
                            <v-text-field v-model="editCar.Automarke"></v-text-field>
                        </td>
                        <td v-else class="fixed-width">{{ car.Automarke }}</td>
                        <td v-if="editCarId === car.Kennzeichen" class="edit-field fixed-width">
                            <v-text-field v-model="editCar.Typ"></v-text-field>
                        </td>
                        <td v-else class="fixed-width">{{ car.Typ }}</td>
                        <td v-if="editCarId === car.Kennzeichen" class="edit-field fixed-width">
                            <v-text-field v-model="editCar.Farbe"></v-text-field>
                        </td>
                        <td v-else class="fixed-width">{{ car.Farbe }}</td>
                        <td class="table-icon fixed-width">
                            <v-btn icon variant="plain" class="delete-button" >
                                <v-icon>mdi-delete</v-icon>
                            </v-btn>
                        </td>
                        <td class="table-icon fixed-width">
                            <v-btn  icon variant="plain" @click="editCarId === car.Kennzeichen ? saveCar(car.Kennzeichen) : editCarDetails(car)">
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
    name: "CarTable",
    components: {
        Checkbox,
        Pagination
    },
    props: {
        cars: {
            type: Array,
            required: true
        },
        editCarId: {
            type: String,
            default: null
        },
        editCar: {
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
            selectedCars: [],
            showDeleteButton: false
        };
    },
    methods: {
        updateSelectedCars(carId, isChecked) {
            if (isChecked) {
                this.selectedCars.push(carId);
            } else {
                this.selectedCars = this.selectedCars.filter(id => id !== carId);
            }
        },
        deleteCar(kennzeichen) {
            console.log('Deleting car:', kennzeichen);
        },
        editCarDetails(car) {
            this.$emit('edit-car', car);
        },
        saveCar(kennzeichen) {
            this.$emit('save-car', kennzeichen);
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
    width: 200px;
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
    border-collapse: separate;
    margin: 25px 0;
    font-size: 0.9em;
    width: 100%;
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