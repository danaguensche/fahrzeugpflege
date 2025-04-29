<!-- Wer diese Seite formatiert kommt in die Hölle -->

<template>
  <v-container class="details-card">

    <!-- Skeleton Loader wenn loading true ist -->
    <template v-if="loading">
      <Loader></Loader>
    </template>
    <!-- Vollständige Ansicht der Daten wenn loading false ist -->

    <template v-else>
      <!-- Header der Karte -->
      <v-card class="rounded-xl" elevation="3">
        <Header :title="headerTitle" :switchEditMode="switchEditMode" :icon="headerIcon">
        </Header>

        <!-- Persönliche Informationen -->
        <v-card-text class="px-4 pt-4 pb-0">
          <v-sheet>
            <InformationHeader :title="'Persönliche Informationen'" :editMode="editMode" :icon="headerIcon"
              :getIconForField="getIconForField">
            </InformationHeader>

            <!-- Ansichtsmodus -->
            <InfoList v-if="!editMode" :details="customerDetails" :labels="labels" :infoKeys="personalInfoKeys"
              :getIconForField="getIconForField">
            </InfoList>


            <!-- Bearbeitungsmodus -->
            <InfoListEditMode v-else :personalInfoKeys="personalInfoKeys" :labels="labels"
              :editedData="editedCustomerData" :getIconForField="getIconForField">
            </InfoListEditMode>
          </v-sheet>

          <!-- Adressinformationen -->

          <v-sheet>

            <div class="pa-4
                        bg-primary-lighten-5
                        rounded-t-lg">
              <span class="text-h6
                          font-weight-medium">
                Adressinformationen
              </span>
            </div>

            <!-- Ansichtsmodus -->
            <v-list class="bg-transparent" v-if="!editMode">

              <template v-for="key in addressInfoKeys" :key="key">

                <v-list-item v-if="customerDetails.data[key] !== undefined">

                  <template v-slot:prepend>
                    <v-icon :icon="getIconForField(key)" color="primary" class="mr-2">
                    </v-icon>
                  </template>

                  <v-list-item-title class="font-weight-medium">
                    {{ labels[key] || key }}
                  </v-list-item-title>

                  <v-list-item-subtitle class="mt-1
                                              text-body-1">

                    <template v-if="customerDetails.data[key] === null || customerDetails.data[key] === ''">
                      <span class="text-grey">Keine Daten vorhanden</span>
                    </template>

                    <template v-else>
                      {{ customerDetails.data[key] }}
                    </template>

                  </v-list-item-subtitle>
                </v-list-item>
                <v-divider v-if="key !== addressInfoKeys[addressInfoKeys.length - 1]">
                </v-divider>
              </template>
            </v-list>

            <!-- Bearbeitungsmodus -->
            <div class="pa-4" v-else>
              <v-form ref="addressInfoForm">

                <template v-for="key in addressInfoKeys" :key="key">
                  <v-row no-gutters class="mb-4">
                    <v-col cols="12">
                      <div class="d-flex
                                  align-center
                                  mb-1">
                        <v-icon :icon="getIconForField(key)" color="primary" class="mr-2">
                        </v-icon>
                        <span class="font-weight-medium">
                          {{ labels[key] }}
                        </span>
                      </div>
                      <v-text-field v-model="editedCustomerData[key]" variant="outlined" density="comfortable"
                        hide-details="auto">
                      </v-text-field>
                    </v-col>
                  </v-row>
                </template>

              </v-form>
            </div>
          </v-sheet>


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

          <!-- Metadaten -->
          <MetaData :labels="labels" :formattedCreatedAt="formattedCreatedAt" :formattedUpdatedAt="formattedUpdatedAt">
          </MetaData>
        </v-card-text>

        <v-card-actions class="pa-4">
          <BackButton></BackButton>
          <v-spacer></v-spacer>

          <!-- Bearbeitungsmodus Aktionen -->
          <template v-if="editMode">
            <CancelButton :saveLoading="saveLoading" @cancelEdit="cancelEdit"></CancelButton>
            <SaveButton :saveLoading="saveLoading" @saveCarData="saveCarData"></SaveButton>
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
import SvgIcon from '@jamescoyle/vue-icon';
import Loader from '../../Details/Loader.vue';
import Header from '../../Details/Header.vue';
import InformationHeader from "../../Details/InformationHeader.vue";
import MetaData from '../../Details/MetaData.vue';
import SnackBar from "../../Details/SnackBar.vue";
import BackButton from '../../CommonSlots/BackButton.vue';
import EditButton from '../../Details/EditButton.vue';
import PrintButton from '../../CommonSlots/PrintButton.vue';
import CancelButton from '../../Details/CancelButton.vue';
import SaveButton from '../../Details/SaveButton.vue';
import InfoList from "../../Details/InfoList.vue";
import InfoListEditMode from "../../Details/InfoListEditMode.vue";

export default {
  name: "Details",
  components: {
    SvgIcon,
    Loader,
    Header,
    InformationHeader,
    MetaData,
    SnackBar,
    BackButton,
    EditButton,
    PrintButton,
    CancelButton,
    SaveButton,
    InfoList,
    InfoListEditMode,
  },
  data() {
    return {
      headerTitle: "Kundendetails",
      headerIcon: "mdi-account",
      editMode: false,
      saveLoading: false,
      customerDetails: {
        data: {}
      },
      editedCustomerData: {},
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
        created_at: "Erstellt am",
        updated_at: "Zuletzt aktualisiert am",
      },
      loading: true,
      error: null,
      snackbar: {
        show: false,
        text: '',
        color: 'success',
      },
    };
  },


  computed: {
    personalInfoKeys() {
      return ['id', 'company', 'firstName', 'lastName', 'email', 'phoneNumber'];
    },

    addressInfoKeys() {
      return ['addressLine', 'postalCode', 'city'];
    },

    formattedCreatedAt() {
      try {
        return this.customerDetails.data?.created_at
          ? new Date(this.customerDetails.data.created_at).toLocaleDateString('de-DE', {
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
        return this.customerDetails.data?.updated_at
          ? new Date(this.customerDetails.data.updated_at).toLocaleDateString('de-DE', {
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
    },
  },

  async created() {
    // Initialisieren der Daten
    this.editedCustomerData = {};
  },

  async mounted() {
    try {
      await this.getCustomer();
    } catch (error) {
      console.error("Fehler beim Laden der Komponente:", error);
      this.error = error.message;
      this.showSnackbar(error.message, 'error');
    } finally {
      this.loading = false;
    }
  },


  methods: {
    async getCustomer() {
      try {
        if (!this.$route?.params?.id) {
          throw new Error("Keine Kunden-ID angegeben");
        }

        const { data } = await axios.get(
          `/api/customer/customerdetails/${this.$route.params.id}`
        );

        if (!data) {
          throw new Error("Keine Daten vom Server erhalten");
        }

        this.customerDetails = data;
        // Kopieren der Daten für die Bearbeitung
        this.editedCustomerData = { ...this.customerDetails.data };
        console.log("Kundendetails:", this.customerDetails);
      } catch (error) {
        this.error = error.response?.data?.message || error.message;
        console.error("Fehler beim Abrufen der Kundendetails:", this.error);
        this.showSnackbar("Fehler beim Laden der Kundendetails: " + this.error, 'error');
      } finally {
        this.loading = false;
      }
    },

    getIconForField(key) {
      const iconMap = {
        id: "mdi-identifier",
        company: "mdi-office-building",
        firstName: "mdi-account",
        lastName: "mdi-account-details",
        email: "mdi-email",
        phoneNumber: "mdi-phone",
        addressLine: "mdi-map-marker",
        postalCode: "mdi-mail",
        city: "mdi-city",
        cars: "mdi-car-multiple"
      };

      return iconMap[key] || "mdi-information-outline";
    },

    goBack() {
      try {
        this.$router.back();
      } catch (e) {
        console.error("Fehler beim Navigieren zurück:", e);
        // Fallback, falls $router.back() nicht funktioniert
        window.history.back();
      }
    },

    switchEditMode() {
      this.editMode = !this.editMode;

      if (this.editMode) {
        this.editedCustomerData = { ...this.customerDetails.data };
      }
    },

    printCustomerDetails() {
      console.log("Drucken der Kundendetails gestartet");
      window.print();
    },

    cancelEdit() {
      this.editMode = false;
      this.editedCustomerData = { ...this.customerDetails.data };
    },

    async saveCustomerData() {
      this.saveLoading = true;
      this.error = null;
      try {
        const response = await axios.put(
          `/api/customer/customerdetails/${this.customerDetails.data.id}`,
          this.editedCustomerData
        );

        // Aktualisieren der Kundendaten nach erfolgreicher Speicherung
        const { data } = await axios.get(
          `/api/customer/customerdetails/${this.$route.params.id}`
        );

        this.customerDetails = data;
        this.editMode = false;
        this.showSnackbar("Kundendaten erfolgreich gespeichert", 'success');
      } catch (error) {
        console.error("Fehler beim Speichern der Kundendaten:", error);
        const errorMessage = error.response?.data?.message || "Fehler beim Speichern der Kundendaten";
        this.showSnackbar(errorMessage, 'error');
      } finally {
        this.saveLoading = false;
      }
    },
  }
};
</script>

<style scoped>
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