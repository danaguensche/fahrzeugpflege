<template>
  <v-container class="details-card">
    <!-- Skeleton Loader wenn loading true ist -->
    <template v-if="loading">
      <Loader></Loader>
    </template>

    <!-- Vollständige Ansicht der Daten wenn loading false ist -->
    <template v-else>
      <!-- Header der Karte -->
      <v-card>
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
            <HeaderWithChip :customerDetails="customerDetails"></HeaderWithChip>
            <CarList v-if="customerDetails.data.cars && customerDetails.data.cars.length > 0"
              :cars="customerDetails.data.cars">
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
      return this.formatDate(this.customerDetails.data?.created_at);
    },

    formattedUpdatedAt() {
      return this.formatDate(this.customerDetails.data?.updated_at);
    },
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
        // Validierung der Eingabedaten, weil Namen in CamelCase sind und Backend sie kleingeschrieben erwartet
        const dataToSend = {
          firstname: this.editedCustomerData.firstName,
          lastname: this.editedCustomerData.lastName,
          email: this.editedCustomerData.email,
          phonenumber: this.editedCustomerData.phoneNumber,
          addressline: this.editedCustomerData.addressLine,
          postalcode: this.editedCustomerData.postalCode,
          city: this.editedCustomerData.city,
          company: this.editedCustomerData.company
        };
        await axios.put(
          `/api/customer/customerdetails/${this.$route.params.id}`,
          dataToSend
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
  }
};
</script>

<style scoped>
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