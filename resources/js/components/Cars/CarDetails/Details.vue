<template>
    <v-container class="card-container">
        <!-- Skeleton Loader wenn loading true ist -->
        <Loader v-if="loading"></Loader>

        <!-- Vollständige Ansicht der Daten wenn loading false ist -->
        <template v-else>
            <!-- Header -->
            <v-card class="card">
                <Header :title="headerTitle" :switchEditMode="switchEditMode" :icon="headerIcon"></Header>

                <ImageCarousel :images="images"></ImageCarousel>

                <!-- Fahrzeug information -->
                <v-card-text class="px-4 pt-4 pb-0">
                    <v-sheet>
                        <InformationHeader :title="'Fahrzeuginformationen'" :editMode="editMode">
                        </InformationHeader>

                        <!-- Ansichtsmodus -->
                        <InfoList v-if="!editMode" :details="carDetails" :labels="labels" :infoKeys="vehicleInfoKeys"
                            :getIconForField="getIconForField">
                        </InfoList>

                        <!-- Bearbeitungsmodus -->
                        <InfoListEditMode v-else :personalInfoKeys="vehicleInfoKeys" :labels="labels"
                            :editedData="editedCarData" :getIconForField="getIconForField">
                        </InfoListEditMode>
                    </v-sheet>

                    <!-- Customer information -->
                    <v-sheet>
                        <DefaultHeader :title="'Kundeninformation'"></DefaultHeader>
                        <CustomerInfoList :customer="carDetails.data.customer" :customerId="carDetails.data.customer_id"
                            :labels="labels">
                        </CustomerInfoList>
                    </v-sheet>

                    <!-- Metadaten -->
                    <MetaData :labels="labels" :formattedCreatedAt="formattedCreatedAt"
                        :formattedUpdatedAt="formattedUpdatedAt">
                    </MetaData>

                </v-card-text>
                <v-card-actions class="pa-4">
                    <BackButton></BackButton>
                    <v-spacer></v-spacer>

                    <!-- Bearbeitungsmodus Aktionen -->
                    <template v-if="editMode">
                        <CancelButton :cancelEdit="cancelEdit"></CancelButton>
                        <SaveButton :saveData="saveCarData"></SaveButton>
                    </template>

                    <!-- Ansichtsmodus Aktionen -->
                    <template v-else>
                        <EditButton :switchEditMode="switchEditMode"></EditButton>
                        <PrintButton></PrintButton>
                    </template>
                </v-card-actions>
            </v-card>
        </template>

        <!-- Snackbar für Benachrichtigungen -->
        <SnackBar v-if="snackbar.show" :text="snackbar.text" :color="snackbar.color" @close="snackbar.show = false">
        </SnackBar>
    </v-container>
</template>

<script>
import axios from "axios";
import Loader from "../../Details/Loader.vue";
import ImageCarousel from "../../Details/ImageCarousel.vue";
import InformationHeader from "../../Details/InformationHeader.vue";
import Header from "../../Details/Header.vue";
import BackButton from "../../CommonSlots/BackButton.vue";
import SnackBar from "../../Details/SnackBar.vue";
import PrintButton from "../../CommonSlots/PrintButton.vue";
import EditButton from "../../Details/EditButton.vue";
import MetaData from "../../Details/MetaData.vue";
import CancelButton from "../../Details/CancelButton.vue";
import SaveButton from "../../Details/SaveButton.vue";
import InfoList from "../../Details/InfoList.vue";
import InfoListEditMode from "../../Details/InfoListEditMode.vue";
import DefaultHeader from "../../Details/DefaultHeader.vue";
import CustomerInfoList from "../../Details/CustomerInfoList.vue";

export default {
    name: "CarDetails",
    components: {
        Loader,
        ImageCarousel,
        InformationHeader,
        Header,
        BackButton,
        SnackBar,
        PrintButton,
        EditButton,
        MetaData,
        CancelButton,
        SaveButton,
        InfoList,
        InfoListEditMode,
        DefaultHeader,
        CustomerInfoList
    },
    data() {
        return {
            carDetails: {
                data: {
                    customer: {
                        id: 0,
                        firstname: '',
                        lastname: '',
                        email: ''
                    },
                }
            },
            editedCarData: {},
            headerTitle: "Fahrzeugdetails",
            headerIcon: "mdi-car",
            labels: {
                id: "ID",
                Kennzeichen: "Kennzeichen",
                Fahrzeugklasse: "Fahrzeugklasse",
                Automarke: "Automarke",
                Typ: "Typ",
                Farbe: "Farbe",
                Sonstiges: "Sonstiges",
                customer_id: "Kunden-ID",
                customer: "Kunde",
                created_at: "Erstellt am",
                updated_at: "Zuletzt aktualisiert am",
            },
            loading: true,
            error: null,
            editMode: false,
            saveLoading: false,
            snackbar: {
                show: false,
                text: '',
                color: 'success',
            },
        };
    },
    computed: {
        vehicleInfoKeys() {
            return ['id', 'Kennzeichen', 'Fahrzeugklasse', 'Automarke', 'Typ', 'Farbe', 'Sonstiges'];
        },
        images() {
            const img = this.carDetails.data?.images;

            if (!img) {
                return [];
            }

            if (Array.isArray(img)) {
                return img.filter(Boolean);
            }

            return img ? [img] : [];
        },
        formattedCreatedAt() {
            return this.formatDate(this.carDetails.data?.created_at);
        },
        formattedUpdatedAt() {
            return this.formatDate(this.carDetails.data?.updated_at);
        }
    },
    async mounted() {
        try {
            await this.getCar();
        } catch (error) {
            this.error = error.message;
            this.showSnackbar(error.message, 'error');
        } finally {
            this.loading = false;
        }
    },
    methods: {
        formatDate(dateString) {
            if (!dateString) return 'Unbekannt';

            try {
                return new Date(dateString).toLocaleDateString('de-DE', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            } catch {
                return 'Ungültiges Datum';
            }
        },

        async getCar() {
            try {
                if (!this.$route?.params?.kennzeichen) {
                    throw new Error("Kein Kennzeichen angegeben");
                }

                const { data } = await axios.get(
                    `/api/cars/cardetails/${this.$route.params.kennzeichen}`
                );

                if (!data) {
                    throw new Error("Keine Daten vom Server erhalten");
                }

                this.carDetails = data;
                this.editedCarData = { ...this.carDetails.data };
            } catch (error) {
                this.error = error.response?.data?.message || error.message;
                this.showSnackbar("Fehler beim Laden der Fahrzeugdetails: " + this.error, 'error');
            } finally {
                this.loading = false;
            }
        },

        switchEditMode() {
            this.editMode = !this.editMode;

            if (this.editMode) {
                this.editedCarData = { ...this.carDetails.data };
            }
        },

        getIconForField(key) {
            const iconMap = {
                id: "mdi-pound",
                Kennzeichen: "mdi-car-info",
                Fahrzeugklasse: "mdi-car-estate",
                Automarke: "mdi-car-side",
                Typ: "mdi-car-select",
                Farbe: "mdi-palette",
                Sonstiges: "mdi-text-box",
            };

            return iconMap[key] || "mdi-information-outline";
        },

        cancelEdit() {
            this.editMode = false;
            this.editedCarData = { ...this.carDetails.data };
        },

        async saveCarData() {
            this.saveLoading = true;
            this.error = null;

            try {
                await axios.put(
                    `/api/cars/cardetails/${this.$route.params.kennzeichen}`,
                    this.editedCarData
                );

                // Aktualisieren der Fahrzeugdaten nach erfolgreicher Speicherung
                const { data } = await axios.get(
                    `/api/cars/cardetails/${this.$route.params.kennzeichen}`
                );

                this.carDetails = data;
                this.editMode = false;
                this.showSnackbar("Fahrzeugdaten erfolgreich gespeichert", 'success');
            } catch (error) {
                const errorMessage = error.response?.data?.message || "Fehler beim Speichern der Fahrzeugdaten";
                this.showSnackbar(errorMessage, 'error');
            } finally {
                this.saveLoading = false;
            }
        },

        showSnackbar(text, color = 'success') {
            this.snackbar = {
                show: true,
                text,
                color,
            };
        },
    }
};
</script>

<style scoped>
.card-container {
    width: 100%;
    height: calc(100vh - 40px);
    padding: 20px;
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
}

.card {
    background-color: #ffffff;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
    transition: all 0.3s ease;
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow-y: auto;
}

@media (max-width: 575.98px) {
    .card-container {
        padding: 10px;
        height: calc(100vh - 20px);

    }

    .card {
        font-size: 14px;
    }
}

@media (min-width: 576px) and (max-width: 767.98px) {
    .card-container {
        padding: 15px;
        height: calc(100vh - 30px);
    }
}

@media (min-width: 768px) and (max-width: 991.98px) {
    .card-container {
        max-width: calc(100% - 80px);
    }
}

@media (min-width: 992px) and (max-width: 1199.98px) {
    .card-container {
        max-width: calc(100% - 250px);
    }
}

@media (min-width: 1200px) {
    .card-container {
        max-width: calc(100% - 280px);
    }
}

.v-card-text {
    flex: 1;
    overflow-y: auto;
}

@media (max-width: 767.98px) {
    .v-card-actions {
        flex-direction: column;
        align-items: stretch;
    }

    .v-card-actions button {
        margin-bottom: 8px;
        width: 100%;
    }

    .v-spacer {
        display: none;
    }
}
</style>