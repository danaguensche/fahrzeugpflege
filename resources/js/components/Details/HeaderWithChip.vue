<template>
    <div class="pa-4
                bg-primary-lighten-5
                d-flex
                align-center">
        <DefaultHeader :title="'Fahrzeuge'"></DefaultHeader>
        <v-chip class="ml-2" color="primary" size="small" variant="flat">
            {{ customerDetails.data.cars && customerDetails.data.cars.length || 0 }}
        </v-chip>
    </div>
</template>

<script>
import DefaultHeader from "./DefaultHeader.vue";
export default {
    name: "HeaderWithChip",
    props: {
        customerDetails: {
            type: Object,
            required: true
        }
    },
    components: {
        DefaultHeader,
    }
};
</script>