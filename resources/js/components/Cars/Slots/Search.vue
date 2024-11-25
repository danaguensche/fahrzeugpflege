<template>
    <input class="searchbar forms-field forms-field-input" type="search" :placeholder="context">
    <img src="../../../../img/icons/search.png" class="search-icon">
    <slot></slot>
    </input>
</template>

<script>
export default {
    name: "Search",
    props: {
        context: {
            type: String,
            default: ""
        }
    },
    methods: {
        _set_context(newtext){
            this.context = newtext;
        }
    }
}
</script>
