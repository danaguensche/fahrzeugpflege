<template>
    <!-- Here we will check the existence of certain props/slots/etc to echo the desired components for the form with the desired attributes -->
    <form v-on:click="testOut" class="report-form">

    </form>
</template>

<script>
import axios from 'axios';
import { mapState } from 'vuex';

export default {
     methods: {
        testOut() {
            async function testHehe() {
                try {
                    //const response = await axios.get(url: '/api/berichte', params: {id: 6});
                    axios({method: 'get', url: 'user/', params: {id: 6}});
                    console.log('Fahrzeug erfolgreich gespeichert:', response);
                } catch (error) {
                    console.error('Fehler beim Speichern des Fahrzeugs:', error);
                }
            }
            testHehe();
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
