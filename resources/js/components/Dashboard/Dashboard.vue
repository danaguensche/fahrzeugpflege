<template>
    <div class="dashboard-page" :class="{ 'dashboard-page-sidebar-opened': isSidebarOpen }">
        <v-container fluid>
            <v-row>
                <v-col cols="12" sm="6" md="3">
                    <OpenJobsWidget></OpenJobsWidget>
                </v-col>

                <v-col cols="12" sm="6" md="3">
                    <TodaysJobsWidget></TodaysJobsWidget>
                </v-col>

                <v-col cols="12" sm="6" md="3">
                    <CustomersWidget></CustomersWidget>
                </v-col>

                <v-col cols="12" sm="6" md="3">
                    <CarsWidget></CarsWidget>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
import { mapState, mapGetters } from 'vuex';
import OpenJobsWidget from './Widgets/OpenJobsWidget.vue';
import WidgetLayout from './Widgets/WidgetLayout.vue';
import axios from 'axios';
import CarsWidget from './Widgets/CarsWidget.vue';
import CustomersWidget from './Widgets/CustomersWidget.vue';
import TodaysJobsWidget from './Widgets/TodaysJobsWidget.vue';
export default {
    name: 'Dashboard',
    components: {
        WidgetLayout,
        OpenJobsWidget,
        CarsWidget,
        CustomersWidget,
        TodaysJobsWidget
    },
    data() {
        return {
            numberOfCars: 0,
        };
    },

    computed: {
        ...mapState(['isSidebarOpen']),
        ...mapGetters('auth', ['isAdminOrTrainer']),
    },

    mounted() {
        this.getNumberOfCars();
        setInterval(() => {
            this.getNumberOfCars();
        }, 10000); 
    },

    methods:{
        getNumberOfCars(){
            axios.get('/api/cars/countcars')
                .then(response => {
                    this.numberOfCars = response.data.count;
                    console.log('Number of cars:', this.numberOfCars);
                })

                .catch(error => {
                    console.error('Error fetching number of cars:', error);
                });
        }
    }
}
</script>

<style scoped>
.dashboard-page {
    margin-left: 150px;
    margin-right: 50px;
    transition: margin-left 0.3s ease;
    font-family: var(--font-family);
}

.dashboard-page-sidebar-opened {
    margin-left: 330px;
    transition: margin-left 0.3s ease;
}

/* Tablet Styles */
@media only screen and (max-width: 1024px) {
    .dashboard-page {
        margin-left: 120px;
        margin-right: 30px;
    }
}

/* Mobile Styles */
@media only screen and (max-width: 768px) {
    .dashboard-page {
        margin-left: 160px;
        margin-right: 20px;
        font-size: 12px;
    }

    .dashboard-page-sidebar-opened {
        margin-left: 260px;
    }
}
</style>