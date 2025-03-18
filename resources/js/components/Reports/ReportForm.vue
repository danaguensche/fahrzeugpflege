<template>
    <InfoFinder v-on="infoFinderHandlers"></InfoFinder>
    <InfoDisplayer v-on:entryChosen="sendEntryToCorrectReport"></InfoDisplayer>
    <!-- Here we will check the existence of certain props/slots/etc to echo the desired components for the form with the desired attributes -->
    <form v-on:click="testOut" class="report-form">
        <FormKundenauftrag v-if="chosenForm == 'kundenauftrag'">

        </FormKundenauftrag>
        <FormUebergabeprotokoll v-else-if="chosenForm == 'uebergabeprotokoll'">

        </FormUebergabeprotokoll>
    </form>
    <div v-on:click="emptySelectedEntry" class="clear-inputs-button">Clear Inputs</div>
</template>

<script>
import axios from 'axios';
import { mapState } from 'vuex';
import InfoFinder from './FormComponents/InfoFinder.vue';
import InfoDisplayer from './FormComponents/InfoDisplayer.vue';
import FormKundenauftrag from './FormKundenauftrag.vue';
import FormUebergabeprotokoll from './FormUebergabeprotokoll.vue';

export default {
     name: "ReportForm",
     components: {
        InfoDisplayer,
        InfoFinder,
        FormKundenauftrag,
        FormUebergabeprotokoll
        // ReportTextEntry imported in the individual FormBlah... components
     },
     data(){
        return {
            chosenForm: this.$route.params.formtype, 
            displayerEntries: [],
            selectedEntry: [],
            infoFinderHandlers: {
                searchChosen: this.loadResultsForInfoDisplayer,
                clearSearch: this.emptyResultsForInfoDisplayer
            },
        }
    },
    provide() {
        return {
            results: computed(() => this.displayerEntries),
            formValues: computed(() => this.selectedEntry),
        }
    },
     methods: {
        sendEntryToCorrectReport(event) {
            // load the data out of the chosen entry
            let entryData = ["columnkey/value data from the chosen entry"];
            this.selectedEntry = entryData;
        },
        loadResultsForInfoDisplayer(event) {
            // api call to get the results

            let searchResults = ["list of the results/entries"];
            this.displayerEntries = searchResults;
        },
        emptyResultsForInfoDisplayer(event) {
            this.displayerEntries = [];
        },
        emptySelectedEntry() {
            // for each row in selectedEntry, loop through the columns and set each value to a blank string
            for (let rowIndex = 0; rowIndex < this.selectedEntry.length; rowIndex++) {
                for (let columnIndex = 0; columnIndex < this.selectedEntry[0].length; columnIndex++) {
                    this.selectedEntry[rowIndex][columnIndex] = "";
                }
            }
        },
        testOut() {
            async function testHehe() {
                try {

                    axios.get(`/api/fahrzeuge`, {
                    params: {
                    //page: 1,
                    //itemsPerPage: 20,
                    sortBy: 'Kennzeichen',
                    sortDesc: false
                    }
                    }).then((response) => {
                    //this.cars = response.data.items;
                    //this.totalPages = Math.ceil(response.data.totalItems / 20);
                    //this.currentPage = page;
                    console.log(response);
                    }).catch((error) => {
                    console.error('Fehler beim Abrufen der Fahrzeuge:', error);
                    });



                    //const response = await axios.get(url: '/api/berichte', params: {id: 6});
                    //axios({method: 'get', url: 'user/', params: {id: 6}});
                    //console.log('Fahrzeug erfolgreich gespeichert:', response);
                } catch (error) {
                    console.error('Fehler beim Speichern des Fahrzeugs:', error);
                }
            }
            /*
            async function anotherTest() {
                axios.post('/api/fahrzeuge', {Kennzeichen: '567', Farbe: 'Blue'}).then(function (response) {console.log(response);}).catch(function (error) {console.log(error);});
            }
            async function finalTest() {
                let id = 2;
                let kennzeichen = 567;
                let newDataObj = {
                    Kennzeichen: 567,
                    Farbe: "Purple"
                }
                axios.put(`/api/fahrzeuge/${kennzeichen}`, [kennzeichen, newDataObj]).then(res => {console.log(res);}).catch(err => {console.log("couldnt be updated");});
            }
                */
            /*
            
            async function saveCar(kennzeichen) {
                let newData = {
                    Kennzeichen: '888',
                    Fahrzeugklasse: 1,
                    Automarke: 'Audi',
                    Typ: 'none',
                    Farbe: 'Green'
                };
                axios.put(`/api/fahrzeuge/${kennzeichen}`, newData).then(function (response) {console.log(response);}).catch(function (error) {console.log(error);});
            }
                */
            //testHehe();
            //anotherTest();
            //saveCar(567);
            //finalTest();
        }
     },
    computed: {
        ...mapState(['isSidebarOpen'])
    }
}
</script>

<style scoped>
.report-form {
    background-color: red;
    width: 100svw;
    height: 100svh;
}
</style>
