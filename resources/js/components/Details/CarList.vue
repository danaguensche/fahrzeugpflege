<template>
    <v-list class="bg-transparent">
        <template v-for="(car, index) in cars" :key="index">
            <v-list-item :to="`/fahrzeuge/fahrzeugdetails/${car.Kennzeichen}`">
                <template v-slot:prepend>
                    <v-icon icon="mdi-car" color="primary" class="mr-2">
                    </v-icon>
                </template>

                <v-list-item-title class="font-weight-medium">
                    {{ car.Kennzeichen }}
                </v-list-item-title>
                <v-list-item-subtitle class="mt-1 text-body-1">
                    {{ car.Automarke }} {{ car.Typ }}
                </v-list-item-subtitle>

                <template v-slot:append>
                    <v-icon color="primary">
                        mdi-chevron-right
                    </v-icon>
                </template>
            </v-list-item>
            <v-divider v-if="index !== cars.length - 1"></v-divider>
        </template>
    </v-list>
</template>

<script>
export default {
    name: 'CarList',
    props: {
        cars: {
            type: Array,
            required: true,
            default: () => []
        }
    }
}
</script>