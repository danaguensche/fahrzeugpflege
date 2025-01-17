<template>
    <div class="table-container" :class="{ 'table-container-sidebar-opened': isSidebarOpen }">
        <div v-if="cars.length === 0">Laden...</div>
        <table v-else class="table table-bordered">
            <thead>
                <tr>
                    <th class="select">Auswählen</th>
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
                    <td class="checkbox">
                        <Checkbox></Checkbox>
                    </td>
                    <td v-if="editCarId === car.Kennzeichen" class="edit-field">
                            <input v-model="editCar.Kennzeichen" />
                    </td>
                    <td v-else>
                        <a href="">{{ car.Kennzeichen }}</a>
                    </td>
                    <td v-if="editCarId === car.Kennzeichen" class="edit-field">
                        <input v-model="editCar.Fahrzeugklasse" />
                    </td>
                    <td v-else>{{ car.Fahrzeugklasse }}</td>
                    <td v-if="editCarId === car.Kennzeichen" class="edit-field">
                        <input v-model="editCar.Automarke" />
                    </td>
                    <td v-else>{{ car.Automarke }}</td>
                    <td v-if="editCarId === car.Kennzeichen" class="edit-field">
                        <input v-model="editCar.Typ" />
                    </td>
                    <td v-else>{{ car.Typ }}</td>
                    <td v-if="editCarId === car.Kennzeichen" class="edit-field">
                        <input v-model="editCar.Farbe" />
                    </td>
                    <td v-else>{{ car.Farbe }}</td>
                    <td class="table-icon">
                        <DeleteButton></DeleteButton>
                    </td>
                    <td class="table-icon">
                        <EditButton
                            @click="editCarId === car.Kennzeichen ? saveCar(car.Kennzeichen) : editCarDetails(car)" />
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
    name: "CarTable",
    components: {
        Checkbox,
        DeleteButton,
        EditButton,
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
    methods: {
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
.edit-field {
    padding: 10px 12px;
}

.edit-field input {
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