<template>
    <div class="alertShadow alertPart"></div>
    <div class="alertArea">
    <section class="alertWindow alertPart" :class="alertTypeClass">
        <h2 class="alertPart">{{ alertHeading }}</h2>
        <p class="alertPart">{{ alertParagraph }}</p>
        <div v-if="alertTypeClass === 'alertTypeConfirmation'" v-on:click="confirmationClicked" class="alertOkay alertPart alertButton">{{ alertOkayButton }}</div>
        <div v-on:click="hideAlert" class="alertClose alertPart alertButton">{{ alertCloseButton }}</div>
    </section>
    </div>
</template>


<script>
    export default {
        name: "AlertBase",
        inject: ['alertVisible'],
        emits: ["confirmation-clicked", "close-alert-clicked"],
        data() {
            return {
                
            }
        },
        props: ['alertHeading', 'alertParagraph', 'alertCloseButton', 'alertOkayButton', 'alertTypeClass'],
        methods: {
            hideAlert() {
                this.$emit('close-alert-clicked');
            },
            confirmationClicked() {
                this.$emit('confirmation-clicked');
            }
        }
    }
</script>



<style scoped>
@import url(../../../css/alerts/alertBase.css);
</style>