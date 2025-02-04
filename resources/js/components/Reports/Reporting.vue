<template>
    <h1>Berichtsauswahl</h1>
    <section class="report-list">
        <a href="reportTypes/uebergabeprotokoll" class="report-link">Übergabeprotokoll</a>
        <a href="reportTypes/kundenauftrag" class="report-link">Kundenauftrag</a>
    </section>
</template>

<script>
import axios from 'axios';
import { mapState } from 'vuex';

export default {
   
}
</script>

<style scoped>
    .report-list {
        background-color: black;
        border: 4px solid var(--primary-color-hover);
        width: 100%;
    }
    .report-link {
        background-color: var(--primary-color);
    }
    @media only screen and (max-width: 650px) {

    }
</style>
