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
      <!-- Header -->
      <v-card class="rounded-xl" elevation="3">
        <v-card-title class="d-flex
                             align-center
                             pa-4
                             bg-primary
                             text-white
                             rounded-t-xl">
          <v-icon size="24" class="mr-2">mdi-car</v-icon>
          <span class="text-h5 font-weight-medium">Fahrzeugdetails</span>
          <v-spacer></v-spacer>
          <v-btn icon @click="switchEditMode"
                 variant="text"
                 color="white">
            <v-tooltip activator="parent" location="bottom">
              Bearbeiten
            </v-tooltip>
            <v-icon>mdi-pencil</v-icon>
          </v-btn>
        </v-card-title>

        <!-- Vehicle images -->
        <div class="px-4 pt-4">
          <template v-if="images.length > 0">
            <v-carousel class="rounded-lg overflow-hidden"
                        height="400"
                        hide-delimiter-background
                        show-arrows="hover"
                        cycle interval="8000"
                        delimiter-icon="mdi-circle"
                        elevation="1">
              <template v-slot:prev="{ props }">
                <v-btn variant="elevated"
                       v-bind="props"
                       class="carousel-arrow"
                       color="primary"
                       rounded="circle">
                  <v-icon>mdi-chevron-left</v-icon>
                </v-btn>
              </template>
              <template v-slot:next="{ props }">
                <v-btn variant="elevated"
                       v-bind="props"
                       class="carousel-arrow"
                       color="primary"
                       rounded="circle">
                  <v-icon>mdi-chevron-right</v-icon>
                </v-btn>
              </template>
              <v-carousel-item v-for="(image, index) in images" :key="index" :src="image" cover>
                <template v-slot:placeholder>
                  <div class="d-flex
                              align-center
                              justify-center
                              fill-height">
                    <v-progress-circular indeterminate
                                         color="primary">
                    </v-progress-circular>
                  </div>
                </template>
                <div class="image-overlay"></div>
              </v-carousel-item>
            </v-carousel>
          </template>

          <!-- Wenn keine Bilder vorhanden sind -->
          <template v-else>
            <v-sheet class="d-flex
                            align-center
                            justify-center
                            rounded-lg
                            bg-grey-lighten-4"
                      height="250">
              <div class="text-center">
                <v-icon size="64" color="grey-darken-1">mdi-image-off</v-icon>
                <div class="text-body-1 text-grey-darken-1 mt-2">
                  Keine Bilder vorhanden
                </div>
              </div>
            </v-sheet>
          </template>
        </div>

        <!-- Fahrzeug information -->
        <v-card-text class="px-4 pt-4 pb-0">
          <v-sheet class="rounded-lg mb-4" elevation="1">
            <div class="pa-4
                        bg-primary-lighten-5
                        rounded-t-lg
                        d-flex align-center
                        justify-space-between">
              <span class="text-h6 font-weight-medium">Fahrzeuginformationen</span>
              <div v-if="editMode">
                <v-chip color="warning" variant="flat">Bearbeitungsmodus</v-chip>
              </div>
              <div v-else>
                <v-chip color="success" variant="flat">Ansichtsmodus</v-chip>
              </div>
            </div>

            <!-- Ansichtsmodus -->
            <v-list class="bg-transparent" v-if="!editMode">
              <template v-for="key in vehicleInfoKeys" :key="key">
                <v-list-item>
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
                    <template v-if="key === 'Kennzeichen'">
                      <v-chip color="primary" variant="flat" class="font-weight-bold">
                        {{ carDetails.data[key] }}
                      </v-chip>
                    </template>
                    <template v-else-if="carDetails.data[key] === null || carDetails.data[key] === '' || carDetails.data[key] === undefined">
                      <span class="text-grey">Keine Daten vorhanden</span>
                    </template>
                    <template v-else>
                      {{ carDetails.data[key] }}
                    </template>
                  </v-list-item-subtitle>
                </v-list-item>
                <v-divider v-if="key !== vehicleInfoKeys[vehicleInfoKeys.length - 1]"></v-divider>
              </template>
            </v-list>

            <!-- Bearbeitungsmodus -->
            <div class="pa-4" v-else>
              <v-form ref="vehicleInfoForm">
                <template v-for="key in vehicleInfoKeys" :key="key">
                  <v-row no-gutters class="mb-4">
                    <v-col cols="12">
                      <div class="d-flex align-center mb-1">
                        <v-icon :icon="getIconForField(key)"
                                color="primary"
                                class="mr-2">
                        </v-icon>
                        <span class="font-weight-medium">
                          {{ labels[key] || key }}
                        </span>
                      </div>
                      <v-text-field v-model="editedCarData[key]"
                                    variant="outlined" 
                                    density="comfortable"
                                    hide-details="auto"
                                    :readonly="key === 'id'" 
                                    :disabled="key === 'id'"
                                    :hint="key === 'id' ? 'ID kann nicht bearbeitet werden' : ''"
                                    :persistent-hint="key === 'id'">
                      </v-text-field>
                    </v-col>
                  </v-row>
                </template>
              </v-form>
            </div>
          </v-sheet>

          <!-- Customer information -->
          <v-sheet class="rounded-lg mb-4" elevation="1">
            <div class="pa-4 bg-primary-lighten-5 rounded-t-lg">
              <span class="text-h6 font-weight-medium">Kundeninformation</span>
            </div>
            <v-list class="bg-transparent">
              <v-list-item>
                <template v-slot:prepend>
                  <v-icon icon="mdi-account" color="primary" class="mr-2"></v-icon>
                </template>
                <v-list-item-title class="font-weight-medium">{{ labels['customer'] }}</v-list-item-title>
                <v-list-item-subtitle class="mt-1 text-body-1">
                  <template v-if="carDetails.data.customer">
                    <v-btn variant="text" color="primary" prepend-icon="mdi-account-details"
                      :to="`/kunden/kundendetails/${carDetails.data.customer.id}`" class="pa-0">
                      {{ carDetails.data.customer.firstname }} {{ carDetails.data.customer.lastname }}
                    </v-btn>
                  </template>
                  <template v-else>
                    <span class="text-grey">Kein Kunde zugeordnet</span>
                  </template>
                </v-list-item-subtitle>
              </v-list-item>
              <v-divider></v-divider>
              <v-list-item>
                <template v-slot:prepend>
                  <v-icon icon="mdi-identifier" color="primary" class="mr-2"></v-icon>
                </template>
                <v-list-item-title class="font-weight-medium">{{ labels['customer_id'] }}</v-list-item-title>
                <v-list-item-subtitle class="mt-1 text-body-1">
                  {{ carDetails.data.customer_id || 'Nicht zugeordnet' }}
                </v-list-item-subtitle>
              </v-list-item>
            </v-list>
          </v-sheet>

          <!-- Metadaten -->
          <v-sheet class="rounded-lg" elevation="1">
            <div class="pa-4 bg-primary-lighten-5 rounded-t-lg">
              <span class="text-h6 font-weight-medium">Metadaten</span>
            </div>
            <v-list class="bg-transparent">
              <v-list-item>
                <template v-slot:prepend>
                  <v-icon icon="mdi-calendar-plus" color="primary" class="mr-2"></v-icon>
                </template>
                <v-list-item-title class="font-weight-medium">{{ labels['created_at'] }}</v-list-item-title>
                <v-list-item-subtitle class="mt-1 text-body-1">
                  {{ formattedCreatedAt }}
                </v-list-item-subtitle>
              </v-list-item>
              <v-divider></v-divider>
              <v-list-item>
                <template v-slot:prepend>
                  <v-icon icon="mdi-calendar-refresh" color="primary" class="mr-2"></v-icon>
                </template>
                <v-list-item-title class="font-weight-medium">{{ labels['updated_at'] }}</v-list-item-title>
                <v-list-item-subtitle class="mt-1 text-body-1">
                  {{ formattedUpdatedAt }}
                </v-list-item-subtitle>
              </v-list-item>
            </v-list>
          </v-sheet>
        </v-card-text>

        <v-card-actions class="pa-4">
          <v-btn variant="outlined" color="primary" prepend-icon="mdi-arrow-left" @click="goBack">
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
                   @click="saveCarData" 
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
                   @click="printCarDetails">
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

export default {
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
    goBack() {
      try {
        this.$router.back();
      } catch (e) {
        console.error("Fehler beim Navigieren zurück:", e);
        // Fallback, falls $router.back() nicht funktioniert
        window.history.back();
      }
    },
    printCarDetails() {
      console.log("Drucken der Fahrzeugdetails gestartet");
      window.print();
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
      this.snackbar.text = text;
      this.snackbar.color = color;
      this.snackbar.show = true;
    }
  }
};
</script>

<style scoped>
.details-card {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

.carousel-arrow {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  z-index: 1;
}

.image-overlay {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 60px;
  background: linear-gradient(0deg, rgba(0, 0, 0, 0.6) 0%, rgba(0, 0, 0, 0) 100%);
}

.v-carousel :deep(.v-carousel__controls) {
  background: linear-gradient(0deg, rgba(0, 0, 0, 0.3) 0%, rgba(0, 0, 0, 0) 100%);
  padding-bottom: 8px;
}

.v-carousel :deep(.v-carousel__controls .v-btn) {
  color: white !important;
}

/* Responsive adjustments */
@media (max-width: 600px) {
  .details-card {
    padding: 12px;
  }

  .v-carousel {
    height: 250px !important;
  }
}

@media print {
  .v-btn {
    display: none !important;
  }
}
</style>