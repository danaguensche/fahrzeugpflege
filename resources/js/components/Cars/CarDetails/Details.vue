<template>
  <v-container class="details-card">
    <!-- Skeleton Loader wenn loading true ist -->
    <Loader v-if="loading"></Loader>

    <!-- Vollständige Ansicht der Daten wenn loading false ist -->
    <template v-else>
      <!-- Header -->
      <v-card class="rounded-xl" elevation="3">
        <Header :title="headerTitle" :switchEditMode="switchEditMode" :icon="headerIcon"></Header>

        <!-- Ehre dass das einfach funktioniert hat -->
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
            <InfoListEditMode v-else :personalInfoKeys="vehicleInfoKeys" :labels="labels" :editedData="editedCarData"
              :getIconForField="getIconForField">
            </InfoListEditMode>
          </v-sheet>

          <!-- Customer information -->
          <v-sheet>
            <DefaultHeader :title="'Kundeninformation'"></DefaultHeader>
            <CustomerInfoList 
              :customer="carDetails.data.customer"
              :customerId="carDetails.data.customer_id"
              :labels="labels">
            </CustomerInfoList>
          </v-sheet>

          <!-- Metadaten -->
          <MetaData :labels="labels" :formattedCreatedAt="formattedCreatedAt" :formattedUpdatedAt="formattedUpdatedAt">
          </MetaData>

        </v-card-text>
        <v-card-actions class="pa-4">
          <BackButton></BackButton>
          <v-spacer></v-spacer>

          <!-- Bearbeitungsmodus Aktionen -->
          <template v-if="editMode">
            <CancelButton :cancelEdit="cancelEdit"></CancelButton>
            <SaveButton :saveCarData="saveCarData"></SaveButton>
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
      try {
        return this.carDetails.data?.created_at
          ? new Date(this.carDetails.data.created_at).toLocaleDateString('de-DE', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
          })
          : 'Unbekannt';
      } catch (e) {
        console.error('Fehler beim Formatieren des Erstellungsdatums:', e);
        return 'Ungültiges Datum';
      }
    },
    formattedUpdatedAt() {
      try {
        return this.carDetails.data?.updated_at
          ? new Date(this.carDetails.data.updated_at).toLocaleDateString('de-DE', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
          })
          : 'Unbekannt';
      } catch (e) {
        console.error('Fehler beim Formatieren des Aktualisierungsdatums:', e);
        return 'Ungültiges Datum';
      }
    }
  },
  async mounted() {
    try {
      await this.getCar();
    } catch (error) {
      console.error("Fehler beim Laden der Komponente:", error);
      this.error = error.message;
      this.showSnackbar(error.message, 'error');
    } finally {
      this.loading = false;
    }
  },
  methods: {
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
        // Kopieren der Daten für die Bearbeitung
        this.editedCarData = { ...this.carDetails.data };
        console.log("Fahrzeugdetails:", this.carDetails);
        console.log("Kundendetails:", this.carDetails.data.customer);
      } catch (error) {
        this.error = error.response?.data?.message || error.message;
        console.error("Fehler beim Abrufen der Fahrzeugdetails:", this.error);
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

    getCustomerIconForField(key) {
      const iconMap = {
        id: "mdi-pound",
        firstname: "mdi-account",
        lastname: "mdi-account",
        email: "mdi-email",
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
        const response = await axios.put(
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
        console.error("Fehler beim Speichern der Fahrzeugdaten:", error);
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
.details-card {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

/* Responsive adjustments */
@media (max-width: 600px) {
  .details-card {
    padding: 12px;
  }
}

@media print {
  .v-btn {
    display: none !important;
  }
}
</style>