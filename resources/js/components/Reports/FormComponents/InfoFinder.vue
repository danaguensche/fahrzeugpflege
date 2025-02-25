<template>
    <form class="report-info-finder">
        <select class="report-column-selection">
            <slot></slot>
        </select>
        <input type="text" name="report-search-term">
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
            
        }
    },

    methods: {
    }
}
</script>

<style scoped>

</style>
