<template>
    <!-- Fahrzeuge -->
    <v-sheet>
        <div class="pa-4
                        bg-primary-lighten-5
                        rounded-t-lg
                        d-flex
                        align-center">

            <span class="text-h6
                           font-weight-medium">
                Fahrzeuge
            </span>

            <v-chip class="ml-2" color="primary" size="small" variant="flat">
                {{ customerDetails.data.cars && customerDetails.data.cars.length || 0 }}
            </v-chip>
        </div>

        <template v-if="customerDetails.data.cars && customerDetails.data.cars.length > 0">
            <v-list class="bg-transparent">

                <template v-for="(car, index) in customerDetails.data.cars" :key="index">
                    <v-list-item :to="`/fahrzeuge/fahrzeugdetails/${car.Kennzeichen}`">

                        <template v-slot:prepend>
                            <v-icon icon="mdi-car" color="primary" class="mr-2">
                            </v-icon>

                        </template>

                        <v-list-item-title class="font-weight-medium">
                            {{ car.Kennzeichen }}
                        </v-list-item-title>
                        <v-list-item-subtitle class="mt-1
                                                 text-body-1">
                            {{ car.Automarke }} {{ car.Typ }}
                        </v-list-item-subtitle>

                        <template v-slot:append>
                            <v-icon color="primary">
                                mdi-chevron-right
                            </v-icon>
                        </template>

                    </v-list-item>
                    <v-divider v-if="index !== customerDetails.data.cars.length - 1"></v-divider>
                </template>

            </v-list>
        </template>


        <!-- Wenn keine Fahrzeuge vorhanden sind -->

        <template v-else>
            <v-list-item>
                <v-list-item-subtitle class="text-grey">
                    <div class="d-flex
                              align-center
                              justify-center
                              pa-4">
                        <v-icon icon="mdi-car-off" color="grey-lighten-1" size="32" class="mr-2">
                        </v-icon>
                        <span>Keine Fahrzeuge zugeordnet</span>
                    </div>
                </v-list-item-subtitle>
            </v-list-item>
        </template>

    </v-sheet>
</template>

<script>
export default {
    name: "CustomerInfoList",
    props: {
        customerDetails: {
            type: Object,
            default: () => ({
                data: {
                    cars: []
                }
            })
        }
    }
}
</script>