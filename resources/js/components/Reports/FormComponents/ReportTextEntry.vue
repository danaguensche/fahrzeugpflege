<template>
<section class="report-text-entry">
    <span>{{rteLabel}}</span>
    <input type="text" name="{{rteKey}}">
</section>
</template>

<script>

export default {
    name: "ReportTextEntry",

    components: {
        
    },

    props: [
        'rteLabel', 'rteKey'
    ],

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
