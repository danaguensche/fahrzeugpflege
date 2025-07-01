<template>
    <input 
        v-model="localSearchString" 
        class="searchbar forms-field forms-field-input" 
        type="search" 
        :placeholder="context"
        @input="handleInput"
    >
</template>

<script>
export default {
    name: "Searchbar",
    props: {
        context: {
            type: String,
            default: ""
        },
        searchText: {
            type: String,
            default: ""
        }
    },
    emits: ['update:searchString', 'clearSearch'],
    data() {
        return {
            localSearchString: this.searchText
        }
    },
    watch: {
        searchText(newValue) {
            this.localSearchString = newValue;
        }
    },
    methods: {
        handleInput() {
            this.$emit('update:searchString', this.localSearchString);
        },
        clearSearch() {
            this.localSearchString = "";
            this.$emit('update:searchString', "");
            this.$emit('clearSearch');
        }
    }
}
</script>

<style scoped>
.searchbar {
    background-color: white;
    margin-top: 50px;
    margin-bottom: 50px;
    width: 100%;
    min-height: 50px;
    padding: 12px 80px 12px 20px;
    border: 1px solid #ddd;
    border-radius: 30px;
    box-shadow: var(--box-shadow);
    font-size: 16px;
    outline: none;
    transition: all 0.2s ease;
}

.searchbar:focus {
    border-color: var(--primary-color, #007bff);
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
}

.searchbar.forms-field {
    border-radius: 30px;
}

@media only screen and (max-width: 650px) {
    .searchbar {
        width: 100%;
        margin-top: 20px;
        margin-bottom: 20px;
        padding-right: 20px;
        font-size: 14px;
        min-height: 45px;
    }
}
</style>