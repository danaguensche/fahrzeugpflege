<template>
  <v-container class="card-container">
    <!-- Skeleton Loader wenn loading true ist -->
    <template v-if="loading">
      <Loader></Loader>
    </template>

    <!-- Vollständige Ansicht der Daten wenn loading false ist -->
    <template v-else>
      <!-- Header der Karte -->
      <v-card class="card">
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
            <DefaultHeader :title="'Adressinformationen'"></DefaultHeader>
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

                  <v-list-item-subtitle class="mt-1 text-body-1">
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
                      <div class="d-flex align-center mb-1">
                        <v-icon :icon="getIconForField(key)" color="primary" class="mr-2">
                        </v-icon>
                        <span class="font-weight-medium">
                          {{ labels[key] }}
                        </span>
                      </div>
                      <v-text-field v-model="editedCustomerData[key]" variant="outlined" density="comfortable"
                        hide-details="auto" class="w-50">
                      </v-text-field>
                    </v-col>
                  </v-row>
                </template>
              </v-form>
            </div>
          </v-sheet>

          <!-- Fahrzeuge --> 
          <v-sheet>
            <HeaderWithChip :customerDetails="customerDetails"></HeaderWithChip>
            <CarList v-if="customerDetails.data.cars && customerDetails.data.cars.length > 0"
              :cars="customerDetails.data.cars" :edit-mode="editMode" @delete-car="deleteCar">
            </CarList>
            <!-- Wenn keine Fahrzeuge vorhanden sind -->
            <template v-else>
              <v-list-item>
                <v-list-item-subtitle class="text-grey">
                  <div class="d-flex align-center justify-center pa-4">
                    <v-icon icon="mdi-car-off" color="grey-lighten-1" size="32" class="mr-2">
                    </v-icon>
                    <span>Keine Fahrzeuge zugeordnet</span>
                  </div>
                </v-list-item-subtitle>
              </v-list-item>
            </template>
            <v-btn class="mt-4" color="primary" @click="openCarAddDialog">
              Fahrzeug hinzufügen
            </v-btn>
          </v-sheet>

          <!-- Fahrzeug hinzufügen Dialog -->
          <CarAddDialog ref="carAddDialog" :kundeId="$route.params.id" @car-added="handleNewCar"
            @car-assigned="handleCarAssigned" @car-selected="handleCarSelected" @error="handleCarAddError">
          </CarAddDialog>

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
            <SaveButton :saveData="saveCustomerData"></SaveButton>
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
import DefaultHeader from "../../Details/DefaultHeader.vue";
import HeaderWithChip from "../../Details/HeaderWithChip.vue";
import CarList from "../../Details/CarList.vue";
import CarAddDialog from "./CarAddDialog.vue";

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
    DefaultHeader,
    HeaderWithChip,
    CarList,
    CarAddDialog,
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
        firstname: "Vorname",
        lastname: "Nachname",
        email: "E-Mail",
        phonenumber: "Telefonnummer",
        addressline: "Straße und Hausnummer",
        postalcode: "Postleitzahl",
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
      return ['id', 'company', 'firstname', 'lastname', 'email', 'phonenumber'];
    },

    addressInfoKeys() {
      return ['addressline', 'postalcode', 'city'];
    },

    formattedCreatedAt() {
      return this.formatDate(this.customerDetails.data?.created_at);
    },

    formattedUpdatedAt() {
      return this.formatDate(this.customerDetails.data?.updated_at);
    }
  },

  async mounted() {
    try {
      await this.getCustomer();
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
        this.editedCustomerData = { ...this.customerDetails.data };
      } catch (error) {
        this.error = error.response?.data?.message || error.message;
        this.showSnackbar("Fehler beim Laden der Kundendetails: " + this.error, 'error');
      } finally {
        this.loading = false;
      }
    },

    showSnackbar(text, color = 'success') {
      this.snackbar = {
        show: true,
        text,
        color,
      };
    },

    getIconForField(key) {
      const iconMap = {
        id: "mdi-identifier",
        company: "mdi-office-building",
        firstname: "mdi-account",
        lastname: "mdi-account-details",
        email: "mdi-email",
        phonenumber: "mdi-phone",
        addressline: "mdi-map-marker",
        postalcode: "mdi-mail",
        city: "mdi-city",
        cars: "mdi-car-multiple"
      };

      return iconMap[key] || "mdi-information-outline";
    },

    switchEditMode() {
      this.editMode = !this.editMode;

      if (this.editMode) {
        this.editedCustomerData = { ...this.customerDetails.data };
      }
    },

    cancelEdit() {
      this.editMode = false;
      this.editedCustomerData = { ...this.customerDetails.data };
    },

    async saveCustomerData() {
      this.saveLoading = true;
      this.error = null;

      try {
        await axios.put(
          `/api/customer/customerdetails/${this.$route.params.id}`,
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
        const errorMessage = error.response?.data?.message || "Fehler beim Speichern der Kundendaten";
        this.showSnackbar(errorMessage, 'error');
      } finally {
        this.saveLoading = false;
      }
    },

    openCarAddDialog() {
      if (this.$refs.carAddDialog) {
        this.$refs.carAddDialog.open();
      }
    },

    async handleNewCar(newCar) {
      try {
        const customerId = this.$route.params.id;

        const requestPayload = {
          customer_id: customerId,
          Kennzeichen: newCar.car.Kennzeichen,
          Fahrzeugklasse: newCar.car.Fahrzeugklasse || null,
          Automarke: newCar.car.Automarke || null,
          Typ: newCar.car.Typ || null,
          Farbe: newCar.car.Farbe || null,
          Sonstiges: newCar.car.Sonstiges || null
        };

        const endpoint = `/api/cars/cardetails/${newCar.car.Kennzeichen}`;

        console.log('Sende Anfrage an:', endpoint);
        console.log('Mit Payload:', JSON.stringify(requestPayload));

        // Setze explizite Header
        const response = await axios.put(
          endpoint,
          requestPayload,
          {
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'X-Requested-With': 'XMLHttpRequest'
            }
          }
        );

        console.log('Server-Antwort:', response.data);

        // Refresh car details to get the latest data
        await this.getCustomer();

        // Show success message
        this.showSnackbar('Fahrzeug erfolgreich hinzugefügt und mit Kunden verknüpft', 'success');
      } catch (error) {
        console.error('Fehler beim Zuweisen des Fahrzeugs:', error);

        // Detaillierte Fehlerinformationen loggen
        if (error.response) {
          console.error('Server-Antwort:', error.response.status);
          console.error('Fehlerdaten:', error.response.data);

          // Verbesserte Fehlerbehandlung für Validierungsfehler
          if (error.response.status === 422 && error.response.data.errors) {
            const validationErrors = Object.values(error.response.data.errors).flat();
            const errorMessage = validationErrors.length > 0
              ? validationErrors.join(', ')
              : error.response.data.message || "Validierungsfehler";
            this.handleCarAddError(errorMessage);
            return;
          }

          console.error('Headers:', error.response.headers);
        } else if (error.request) {
          console.error('Keine Antwort erhalten:', error.request);
        } else {
          console.error('Fehler beim Erstellen der Anfrage:', error.message);
        }

        const errorMessage = error.response?.data?.message || "Fehler beim Aktualisieren des Kunden";
        this.handleCarAddError(errorMessage);
      }
    },

    handleCarAddError(errorMessage) {
      this.showSnackbar(errorMessage, 'error');
    },

    async handleCarAssigned(data) {
      try {
        // Refresh car details to get the latest data
        await this.getCustomer();

        // Show success message
        this.showSnackbar(`Fahrzeug ${data.car.Kennzeichen} erfolgreich mit Kunden verknüpft`, 'success');
      } catch (error) {
        const errorMessage = error.response?.data?.message || "Fehler beim Aktualisieren der Kundenfahrzeuge";
        this.showSnackbar(errorMessage, 'error');
      }
    },

    handleCarSelected(car) {
      // Dieser Handler wird aufgerufen, wenn ein Auto aus der Liste ausgewählt wurde
      console.log('Auto ausgewählt:', car);
    },

    async deleteCar(car) {
      if (confirm(`Möchten Sie das Fahrzeug ${car.Kennzeichen} wirklich von diesem Kunden entfernen?`)) {
        try {
          await axios.delete(`/api/customer/${this.$route.params.id}/car/${car.id}`);
          this.showSnackbar('Fahrzeug erfolgreich vom Kunden entfernt', 'success');
          await this.getCustomer(); // Refresh customer details
        } catch (error) {
          const errorMessage = error.response?.data?.message || "Fehler beim Entfernen des Fahrzeugs";
          this.showSnackbar(errorMessage, 'error');
        }
      }
    }
  }
}
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