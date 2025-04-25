<!-- Wer diese Seite formatiert kommt in die Hölle -->

<template>
  <v-container class="details-card">

    <!-- Skeleton Loader wenn loading true ist -->
    <template v-if="loading">
      <v-card class="pa-6 rounded-xl" elevation="2">
        <v-skeleton-loader
          type="heading,
                list-item-three-line,
                table-heading,
                table-row-divider,
                table-row,
                table-row-divider,
                table-row">
        </v-skeleton-loader>
      </v-card>
    </template>


    <!-- Vollständige Ansicht der Daten wenn loading false ist -->

    <template v-else>

      <!-- Header der Karte -->
      <v-card class="rounded-xl"
              elevation="3">

        <v-card-title class="d-flex
                            align-center
                            pa-4 bg-primary
                            text-white
                            rounded-t-xl">

          <v-icon size="24"
                  class="mr-2">
                  mdi-account
          </v-icon>

          <span class="text-h5
                       font-weight-medium">
            Kundendetails
          </span>

          <v-spacer></v-spacer>

          <v-btn icon @click="switchEditMode"
                      variant="text"
                      color="white">

            <v-tooltip activator="parent"
                       location="bottom">
                       Bearbeiten
            </v-tooltip>

            <v-icon>mdi-pencil</v-icon>
          </v-btn>

        </v-card-title>


        <!-- Persönliche Informationen -->
        <v-card-text class="px-4
                            pt-4
                            pb-0">

          <v-sheet class="rounded-lg
                          mb-4"
                   elevation="1">

            <div class="pa-4
                        bg-primary-lighten-5
                        rounded-t-lg
                        d-flex align-center
                        justify-space-between">

              <span class="text-h6
                          font-weight-medium">
                          Persönliche Informationen
              </span>

              <div v-if="editMode">
                <v-chip color="warning"
                        variant="flat">
                        Bearbeitungsmodus
              </v-chip>
              </div>
              <div v-else>
                <v-chip color="success"
                        variant="flat">
                        Ansichtsmodus
                </v-chip>
              </div>
            </div>

            <!-- Ansichtsmodus -->
            <v-list class="bg-transparent"
                    v-if="!editMode">
              <template v-for="key in personalInfoKeys" :key="key">

                <v-list-item v-if="customerDetails.data[key] !== undefined">

                  <template v-slot:prepend>
                    <v-icon :icon="getIconForField(key)"
                            color="primary" 
                            class="mr-2">
                  </v-icon>
                  </template>

                  <v-list-item-title class="font-weight-medium">
                    {{ labels[key] || key }}
                  </v-list-item-title>

                  <v-list-item-subtitle class="mt-1 text-body-1">

                    <template v-if="customerDetails.data[key] === null || customerDetails.data[key] === ''">
                      <span class="text-grey">
                        Keine Daten vorhanden
                      </span>
                    </template>

                    <template v-else>
                      {{ customerDetails.data[key] }}
                    </template>

                  </v-list-item-subtitle>
                </v-list-item>
                <v-divider v-if="key !== personalInfoKeys[personalInfoKeys.length - 1]"></v-divider>
              </template>
            </v-list>


            <!-- Bearbeitungsmodus -->

            <div class="pa-4" v-else>
              <v-form ref="personalInfoForm">
                <template v-for="key in personalInfoKeys" :key="key">
                  <v-row no-gutters class="mb-4">
                    <v-col cols="12">
                      <div class="d-flex align-center mb-1">
                        <v-icon :icon="getIconForField(key)"
                                color="primary"
                                class="mr-2">
                        </v-icon>
                        <span class="font-weight-medium">
                          {{ labels[key] }}
                        </span>
                      </div>
                      <v-text-field v-model="editedCustomerData[key]"
                                    variant="outlined" 
                                    density="comfortable"
                                    hide-details="auto"
                                    :readonly="key === 'id'" 
                                    :disabled="key === 'id'"
                                    :hint="key === 'id' ? 'Kundennummer kann nicht bearbeitet werden' : ''"
                                    :persistent-hint="key === 'id'">
                      </v-text-field>
                    </v-col>
                  </v-row>
                </template>
              </v-form>
            </div>
          </v-sheet>

          <!-- Adressinformationen -->

          <v-sheet class="rounded-lg 
                          mb-4"
                   elevation="1">

            <div class="pa-4
                        bg-primary-lighten-5
                        rounded-t-lg">
              <span class="text-h6
                          font-weight-medium">
                Adressinformationen
              </span>
            </div>

            <v-list class="bg-transparent"
                          v-if="!editMode">

              <template v-for="key in addressInfoKeys" :key="key">

                <v-list-item v-if="customerDetails.data[key] !== undefined">

                  <template v-slot:prepend>
                    <v-icon :icon="getIconForField(key)"
                            color="primary"
                            class="mr-2">
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
                  <v-row no-gutters
                         class="mb-4">
                    <v-col cols="12">
                      <div class="d-flex
                                  align-center
                                  mb-1">
                        <v-icon :icon="getIconForField(key)"
                                 color="primary" 
                                 class="mr-2">
                        </v-icon>
                        <span class="font-weight-medium">
                          {{ labels[key] }}
                        </span>
                      </div>
                      <v-text-field v-model="editedCustomerData[key]"
                                    variant="outlined"
                                    density="comfortable"
                                    hide-details="auto">
                      </v-text-field>
                    </v-col>
                  </v-row>
                </template>

              </v-form>
            </div>
          </v-sheet>


          <!-- Fahrzeuge -->
          <v-sheet class="rounded-lg mb-4"
                   elevation="1">
            <div class="pa-4
                        bg-primary-lighten-5
                        rounded-t-lg
                        d-flex
                        align-center">

              <span class="text-h6
                           font-weight-medium">
                           Fahrzeuge
              </span>

              <v-chip class="ml-2"
                      color="primary"
                      size="small"
                      variant="flat">
                {{ customerDetails.data.cars && customerDetails.data.cars.length || 0 }}
              </v-chip>
            </div>

            <template v-if="customerDetails.data.cars && customerDetails.data.cars.length > 0">
              <v-list class="bg-transparent">
                
                <template v-for="(car, index) in customerDetails.data.cars" :key="index">
                  <v-list-item :to="`/fahrzeuge/fahrzeugdetails/${car.Kennzeichen}`">
                    
                    <template v-slot:prepend>
                      <v-icon icon="mdi-car"
                              color="primary"
                              class="mr-2">
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
                    <v-icon icon="mdi-car-off"
                            color="grey-lighten-1"
                            size="32"
                            class="mr-2">
                    </v-icon>
                    <span>Keine Fahrzeuge zugeordnet</span>
                  </div>
                </v-list-item-subtitle>
              </v-list-item>
            </template>

          </v-sheet>

          <!-- Metadaten -->
          <v-sheet class="rounded-lg"
                   elevation="1">
            <div class="pa-4
                        bg-primary-lighten-5
                        rounded-t-lg">
              <span class="text-h6
                           font-weight-medium">
                           Metadaten
              </span>
            </div>
            <v-list class="bg-transparent">
              <v-list-item>

                <template v-slot:prepend>
                  <v-icon icon="mdi-calendar-plus"
                          color="primary"
                          class="mr-2">
                  </v-icon>
                </template>

                <v-list-item-title class="font-weight-medium">
                  {{ labels.createdAt || 'Erstellt am' }}
                </v-list-item-title>
                <v-list-item-subtitle class="mt-1 text-body-1">
                  {{ formattedCreatedAt }}
                </v-list-item-subtitle>
              </v-list-item>
              <v-divider></v-divider>
              <v-list-item>
                
                <template v-slot:prepend>
                  <v-icon icon="mdi-calendar-refresh"
                          color="primary"
                          class="mr-2">
                  </v-icon>
                </template>

                <v-list-item-title class="font-weight-medium">
                  {{ labels.updatedAt || 'Zuletzt aktualisiert am'}}
                </v-list-item-title>
                <v-list-item-subtitle class="mt-1
                                             text-body-1">
                  {{ formattedUpdatedAt }}
                </v-list-item-subtitle>
              </v-list-item>
            </v-list>
          </v-sheet>
        </v-card-text>

        <v-card-actions class="pa-4">
          <v-btn variant="outlined"
                 color="primary"
                 prepend-icon="mdi-arrow-left"
                 @click="goBack">
            Zurück
          </v-btn>
          <v-spacer></v-spacer>

          <!-- Bearbeitungsmodus Aktionen -->
          <template v-if="editMode">
            <v-btn variant="tonal"
                   color="error" 
                   prepend-icon="mdi-cancel" 
                   @click="cancelEdit" 
                   :loading="saveLoading">
              Abbrechen
            </v-btn>
            <v-btn variant="elevated"
                   color="success"
                   prepend-icon="mdi-content-save"
                   class="ml-2"
                   @click="saveCustomerData" 
                   :loading="saveLoading">
              Speichern
            </v-btn>
          </template>

          <!-- Ansichtsmodus Aktionen -->
          <template v-else>
            <v-btn variant="tonal"
                   color="secondary"
                   prepend-icon="mdi-file-document-edit"
                   @click="switchEditMode">
              Bearbeiten
            </v-btn>
            <v-btn variant="elevated"
                   color="primary" 
                   prepend-icon="mdi-printer"
                   class="ml-2"
                  @click="printCustomerDetails">
              Drucken
            </v-btn>
          </template>
        </v-card-actions>
      </v-card>
    </template>

    <!-- Snackbar für Benachrichtigungen -->
    <v-snackbar v-model="snackbar.show"
                :color="snackbar.color"
                timeout="3000">
      {{ snackbar.text }}

      <template v-slot:actions>
        <v-btn variant="text"
        @click="snackbar.show = false">
          Schließen
        </v-btn>
      </template>
      
    </v-snackbar>
  </v-container>
</template>

<script>
import axios from "axios";
import SvgIcon from '@jamescoyle/vue-icon';

export default {
  name: "Details",
  components: {
    SvgIcon,
  },
  data() {
    return {
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
        createdAt: "Erstellt am",
        updatedAt: "Zuletzt aktualisiert am",
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
        return this.customerDetails.data?.createdAt
          ? new Date(this.customerDetails.data.createdAt).toLocaleDateString('de-DE', {
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
        return this.customerDetails.data?.updatedAt
          ? new Date(this.customerDetails.data.updatedAt).toLocaleDateString('de-DE', {
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

    showSnackbar(text, color = 'success') {
      this.snackbar.text = text;
      this.snackbar.color = color;
      this.snackbar.show = true;
    }
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