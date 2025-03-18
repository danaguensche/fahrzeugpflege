<template>
    <!--
    <section class="report-info-displayer">
        <div v-if="QUERYRESULT.length < 1">
            No Results
        </div>
        <div v-else v-for="VARIABLE in QUERYRESULT" v-on:click="entryClicked">
            {{CONCATENATION STUFF VARIABLE.BLAH + VARIABLE.BLAH2}}
        </div>
    </section>
-->
<section class="report-info-displayer">
    <div v-if="this.displayerEntries.length < 1">
        No Results
    </div>
    <div v-else v-for="entryRow in this.displayerEntries">
        <div v-on:click="entryClicked">{{ entryRow.join(", ") }}</div>
    </div>
</section>
</template>

<script>

export default {
    name: "InfoDisplayer",
    inject: {
        results: {
            from: 'displayerEntries',
            default: []
        }
    },
    components: {
        
    },

    props: {
        
    },

    data(){
        return {
            
        }
    },

    methods: {
        entryClicked(event) {
            this.$emit('entry-chosen', event.target);
        }
    }
}
</script>

<style scoped>

</style>
