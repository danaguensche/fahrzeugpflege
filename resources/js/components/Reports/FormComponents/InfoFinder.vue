<template>
    <form class="report-info-finder">
        <select v-on:change="updatedSearch" class="report-column-selection">
            <slot></slot>
        </select>
        <input v-on:keyup="updatedSearch" type="text" name="report-search-term">
    </form>
</template>

<script>

export default {
    name: "InfoFinder",

    components: {
        
    },

    props: {
        
    },

    data(){
        return {
            searchArea: document.getElementsByTagName("report-search-term")[0]
        }
    },

    methods: {
        updatedSearch(event) {
            if (this.searchArea.value.length < 1) {
                // Clear results in InfoDisplayer
                this.$emit('clear-search', event.target);
                return;
            }
            this.$emit('search-chosen', event.target);
        }
    }
}
</script>

<style scoped>

</style>
