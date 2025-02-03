<template>
    <input v-model="searchText" class="searchbar forms-field forms-field-input" type="search" :placeholder="context">
    <SearchButton></SearchButton>
    <CloseButton :isVisible="!!searchText" @close="clearSearch"></CloseButton>

    <slot></slot>
    </input>
</template>

<script>
import SearchButton from './SearchButton.vue';
import CloseButton from './CloseButton.vue'

export default {
    name: "Search",

    components: {
        SearchButton,
        CloseButton
    },

    props: {
        context: {
            type: String,
            default: ""
        },

        data: {
            type: Array,
            required: true
        }
    },

    data() {
        return {
            searchText: ""
        }
    },

    computed: {

    },

    methods: {
        _set_context(newtext) {
            this.context = newtext;
        },

        clearSearch() {
            this.searchText = "";
        }
    }
}
</script>

<style scoped>
.search-container {
    display: flex;
    align-items: center;
    justify-content: flex-end;
}

.searchbar {
    margin-right: 10px;
}

.searchbar {
    margin-top: 50px;
    margin-bottom: 100px;
    width: 100%;
    box-shadow: var(--box-shadow);

}

.searchbar.forms-field {
    border-radius: 30px;
}
</style>
