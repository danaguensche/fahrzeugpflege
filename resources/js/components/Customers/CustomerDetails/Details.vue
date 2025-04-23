<template>
    <template v-if="loading">
        <v-card title="Kundendetails" variant="outlined" :loading="true">
        </v-card>
    </template>
    <template v-else>
        <v-card>
            <v-card-title>
                Kundendetails
                <v-spacer></v-spacer>
            </v-card-title>
            <v-table>
                <tbody>
                    <tr v-for="(value, key) in customerDetails.data" :key="key">

                        <template v-if="key === 'createdAt'">
                            <td class="text-left">{{ labels[key] || key }}</td>
                            <td class="text-left">{{ formattedCreatedAt }}</td>
                        </template>
                        <template v-else-if="key === 'updatedAt'">
                            <td class="text-left">{{ labels[key] || key }}</td>
                            <td class="text-left">{{ formattedUpdatedAt }}</td>
                        </template>
                        <template v-else-if="key === 'cars'">
                            <td class="text-left">{{ labels[key] || key }}</td>
                            <td class="text-left">
                                <span v-if="value && value.length > 0">
                                    <div v-for="(car, index) in value" :key="index">
                                        <a :href="`/fahrzeuge/fahrzeugdetails/${car.Kennzeichen}`">{{ car.Kennzeichen }}
                                            - {{ car.Automarke }} {{ car.Typ }}</a>
                                    </div>
                                </span>
                                <span v-else>
                                    Keine Fahrzeuge zugeordnet
                                </span>
                            </td>
                        </template>
                        <template v-else>
                            <td class="text-left">{{ labels[key] || key }}</td>
                            <td class="text-left">{{ value }}</td>
                        </template>
                    </tr>
                </tbody>
            </v-table>
        </v-card>
    </template>
</template>

<script>
import axios from "axios";

export default {
    data() {
        return {
            customerDetails: {
                data: {}
            },
            labels: {
                id: "Kundennummer",
                company: "Firma",
                firstName: "Vorname",
                lastName: "Nachname",
                email: "E-Mail",
                phoneNumber: "Telefonnummer",
                addressLine: "Adresse",
                postalCode: "Postleitzahl",
                city: "Stadt",
                cars: "Fahrzeuge",
                createdAt: "Erstellt am",
                updatedAt: "Zuletzt aktualisiert am",
            },
            loading: true,
            error: null
        };
    },
    computed: {
        formattedDetails() {
            return JSON.stringify(this.customerDetails, null, 2);
        },
        formattedCreatedAt() {
            return this.customerDetails.data?.createdAt
                ? new Date(this.customerDetails.data.createdAt).toLocaleDateString('de-DE')
                : 'Unbekannt';
        },
        formattedUpdatedAt() {
            return this.customerDetails.data?.updatedAt
                ? new Date(this.customerDetails.data.updatedAt).toLocaleDateString('de-DE')
                : 'Unbekannt';
        },
        linkToCustomer() {
            return this.customerDetails.data?.id
                ? `/kunden/kundendetails/${this.customerDetails.data.id}`
                : '#';
        }
    },
    async mounted() {
        console.log("Empfangener Parameter:", this.$route.params.id);
        console.log("Vollständige Route:", this.$route);
        await this.getCustomer();
    },
    methods: {

        async getCustomer() {
            try {
                const { data } = await axios.get(
                    `/api/customer/customerdetails/${this.$route.params.id}`
                );
                this.customerDetails = data;
                console.log("Kundendetails:", this.customerDetails);
            } catch (error) {
                this.error = error.response?.data?.message || error.message;
                console.error("Fehler beim Laden der Kundendetails:", this.error);
            } finally {
                this.loading = false;
            }
        }
    }
};
</script>

<style scoped>
/* Grundlegende Stile */
.v-table {
    margin-top: 8px;
}
</style>