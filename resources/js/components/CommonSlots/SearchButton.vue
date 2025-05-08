<template>
    <v-tooltip text="Suchen">
        <v-btn 
            :icon="true"
            :prepend-icon="'mdi-magnify'"
            class="search-button"
        > Suche
        </v-btn>
    </v-tooltip>
</template>

<script>
export default {
    name: "SearchButton",
}
</script>

<style scoped>
.search-button {
    align-self: flex-end;
    position: absolute;
}
</style>
