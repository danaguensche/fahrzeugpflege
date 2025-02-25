<template>
<form></form>
<!--v-model="itemNumber"-->
<ReportTextEntry rteLabel="Bob" rteKey="bobby"></ReportTextEntry>
</template>

<script>
import axios from 'axios';
import { mapState } from 'vuex';
import ReportTextEntry from './FormComponents/ReportTextEntry.vue';

export default {
    name: "FormKundenauftrag",
    components: {
        ReportTextEntry
    },
    inject: {
        formValues: {
            from: 'selectedEntry',
            default: []
        }
        /*
            All the different form input elements inside <template> will be given v-model to be bound to corresponding
            values in the formValues associative array. When formValues changes, it will automatically change the affected inputs, and the other
            way around.
        */
    },
    computed: {
        ...mapState(['isSidebarOpen'])
    },
    data(){
        return {
            
        }
    },
}
</script>

<style scoped>
form {
        background-color: green;
        height: 50svh;
        width: 50svw;
    }
</style>
