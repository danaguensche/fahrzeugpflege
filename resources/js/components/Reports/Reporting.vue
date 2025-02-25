<template>
    <main class="reports-page-content" :class="{ 'reports-page-sidebar-opened': isSidebarOpen }">
        <h1>Berichtsauswahl</h1>
        <section class="report-list">
            <a href="berichte/form/uebergabeprotokoll" class="report-link">Übergabeprotokoll</a>
            <a href="berichte/form/kundenauftrag" class="report-link">Kundenauftrag</a>
        </section>
    </main>
</template>

<script>
import axios from 'axios';
import { mapState } from 'vuex';

export default {
   computed: {
        ...mapState(['isSidebarOpen'])
    }
}
</script>

<style scoped>
    .reports-page-content {
        padding-left: 13svh;
        color: var(--text-color-dark);
        background-color: snow;
        font-family: var(--font-family);
        min-height: 99vh;
        display: grid;
        grid-template-rows: max-content;
    }
    .reports-page-sidebar-opened {
        padding-left: 29svh;
    }
    h1 {
        border-bottom: 2px solid var(--primary-color);
        margin-bottom: 2svh;
        font-size: 1.8rem;
        font-family: inherit;
    }
    .report-list {
        background-color: black;
        border: 4px solid var(--primary-color);
        border-radius: calc(var(--border-radius)*0.8);
        display: grid;
        grid-auto-rows: minmax(50px, auto);
        justify-items: center;
        align-items: center;
        row-gap: 0.5svh;
        padding: 0.4vmin;
        min-height: 100%;
    }
    .report-link {
        background-color: var(--secondary-color);
        text-decoration: none;
        width: 100%;
        height: 100%;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        font-family: inherit;
        border-radius: var(--border-radius);
        color: inherit;
    }
    @media only screen and (max-width: 650px) {

    }
</style>
