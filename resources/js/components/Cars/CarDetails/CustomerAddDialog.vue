<!-- CustomerAddDialog.vue -->
<template>
  <v-dialog v-model="dialog" max-width="700px">
    <v-card class="pa-2">
      
      <v-card-title class="headline primary white--text py-5">
        <v-icon left large color="white">mdi-account-search</v-icon>
        <span class="text-h5">Kundenauswahl</span>
      </v-card-title>

      <template v-if="step === 'checkExisting'">

        <!-- Schritt: Vorhandenen Kunden suchen -->
        <v-card-text class="pa-7">
          
          <v-row>
            <v-col cols="12">
              <v-form ref="existingCustomerForm" v-model="existingFormValid">
                <v-text-field   v-model="existingCustomerSearch" 
                                label="Suchen Sie nach einem Kunden"
                                :rules="[v => !!v || 'Suchbegriff ist erforderlich']" 
                                @keyup.enter="searchCustomer"
                                prepend-icon="mdi-magnify" 
                                clearable outlined 
                                class="rounded-lg">
                </v-text-field>
              </v-form>
            </v-col>
          </v-row>

          <!-- Suchergebnisse -->
          <v-card v-if="searchResults.length > 0" class="mt-4" style="max-height: 500px; overflow-y: auto;">
            
            <v-card-title>
              <v-icon left class="mr-4" color="primary">mdi-format-list-bulleted</v-icon>
              <span class="ga-10"></span>
              Gefundene Kunden ({{ searchResults.length }})
            </v-card-title>

            <v-list two-line>
              <v-list-item v-for="customer in searchResults" :key="customer.id"
                @click="selectExistingCustomer(customer)" class="list-item-hover">

                <template v-slot:prepend>
                  <v-avatar>
                    <v-icon color="primary">mdi-account</v-icon>
                  </v-avatar>
                </template>

                <v-list-item-title class="font-weight-bold">
                  {{ customer.firstname }} {{ customer.lastname }}
                </v-list-item-title>
                <v-list-item-subtitle>
                  {{ customer.email }} - {{ customer.phonenumber }}
                </v-list-item-subtitle>

                <template v-slot:append>
                  <v-btn icon variant="plain" @click.stop="selectExistingCustomer(customer)">
                    <v-icon color="primary">mdi-plus-circle</v-icon>
                  </v-btn>
                </template>
              </v-list-item>
            </v-list>
          </v-card>

          <!-- Keine Suchergebnisse -->
          <v-alert  v-if="showNoResultsMessage" 
                    type="info" 
                    text class="mt-4 rounded-lg">
            <div class="text-center">
              <v-icon large color="info" class="mb-2">mdi-account-off-outline</v-icon>
              <p class="mb-2">Kein Kunde gefunden.</p>
            </div>
          </v-alert>

          <!-- Erfolgsmeldung -->
          <v-snackbar v-model="showSuccessMessage" color="success" timeout="3000" top>
            <v-icon left>mdi-check-circle</v-icon>
            {{ successMessage }}
          </v-snackbar>
        </v-card-text>


        <!-- Buttons zum Kunden hinzufügen, suchen, oder Dialog schließen -->
        <v-card-actions class="pa-4 grey lighten-4">
          <v-btn  color="green lighten-4"
                  @click="openAddCustomerDialog" 
                  prepend-icon="mdi-plus" 
                  size="small"
                  class="mb-2">
            Kunden hinzufügen
          </v-btn>
          <v-spacer></v-spacer>

          <v-btn color="grey darken-1" text @click="closeDialog">
            <v-icon left>mdi-close</v-icon>
            Abbrechen
          </v-btn>

          <v-btn color="primary" @click="searchCustomer" :disabled="!existingFormValid" :loading="isSearching">
            <v-icon left>mdi-magnify</v-icon>
            Kunde suchen
          </v-btn>
        </v-card-actions>
      </template>

      <!-- Neuer Kunde Schritt -->
      <AddCustomerForm v-model="showAddCustomerDialog" @car-added="handleCustomerAdded" :showCarField="false" />

    </v-card>
  </v-dialog>
</template>

<script>
import axios from 'axios';
import AddCustomerForm from '../../Customers/addCustomer/AddCustomerForm.vue';

export default {
  name: 'CustomerAddDialog',
  components: {
    AddCustomerForm
  },
  data() {
    return {
      showAddCustomerDialog: false,
      dialog: false,
      step: 'checkExisting',
      valid: false,
      existingFormValid: false,
      existingCustomerSearch: '',
      searchResults: [],
      showNoResultsMessage: false,
      isSearching: false,
      isSaving: false,
      showSuccessMessage: false,
      successMessage: '',
      newCustomer: {
        company: '',
        firstname: '',
        lastname: '',
        email: '',
        phonenumber: '',
        addressline: '',
        postalcode: '',
        city: '',
      },
    };
  },

  methods: {

    openAddCustomerDialog() {
      this.showAddCustomerDialog = true;
    },
    handleCustomerAdded() {
      this.showAddCustomerDialog = false;
    },

    open() {
      this.dialog = true;
      this.step = 'checkExisting';
      this.resetForm();
    },

    closeDialog() {
      this.dialog = false;
      this.resetForm();
    },

    resetForm() {
      this.newCustomer = {
        company: '',
        firstname: '',
        lastname: '',
        email: '',
        phonenumber: '',
        addressline: '',
        postalcode: '',
        city: '',
      };
      this.existingCustomerSearch = '';
      this.searchResults = [];
      this.showNoResultsMessage = false;
      this.showSuccessMessage = false;
      this.successMessage = '';
      this.isSearching = false;
      this.isSaving = false;

      this.$nextTick(() => {
        if (this.$refs.customerForm) {
          this.$refs.customerForm.resetValidation();
        }
        if (this.$refs.existingCustomerForm) {
          this.$refs.existingCustomerForm.resetValidation();
        }
      });
    },

    async searchCustomer() {
      if (this.$refs.existingCustomerForm && !this.$refs.existingCustomerForm.validate()) {
        return;
      }

      this.isSearching = true;
      this.showNoResultsMessage = false;

      try {
        const response = await axios.get('/api/customers/search', {
          params: { query: this.existingCustomerSearch }
        });

        this.searchResults = Array.isArray(response.data.data) ? response.data.data : [];
        this.showNoResultsMessage = this.searchResults.length === 0;
      } catch (error) {
        console.error('Fehler bei der Kundensuche:', error);
        this.$emit('error', error.response?.data?.message || 'Fehler bei der Kundensuche');
        this.searchResults = [];
        this.showNoResultsMessage = true;
      } finally {
        this.isSearching = false;
      }
    },

    selectExistingCustomer(customer) {
      this.successMessage = `${customer.firstname} ${customer.lastname} wurde ausgewählt!`;
      this.showSuccessMessage = true;

      setTimeout(() => {
        this.$emit('customer-selected', customer);
        this.closeDialog();
      }, 1500);
    },

    async saveCustomer() {
      // Formularvalidierung
      if (this.$refs.customerForm && !this.$refs.customerForm.validate()) {
        return;
      }

      this.isSaving = true;

      try {
        console.log('Neuer Kunde:', this.newCustomer);
        const response = await axios.post('/api/customers', this.newCustomer);

        if (response.data) {
          const newCustomer = response.data;
          this.successMessage = `Neuer Kunde ${newCustomer.firstname} ${newCustomer.lastname} wurde erfolgreich erstellt!`;
          this.showSuccessMessage = true;

          setTimeout(() => {
            this.$emit('customer-added', newCustomer);
            this.closeDialog();
          }, 1500);
        } else {
          throw new Error('Keine Daten vom Server erhalten');
        }
      } catch (error) {
        console.error('Fehler beim Hinzufügen des Kunden:', error);
        const errorMessage = error.response?.data?.message || 'Fehler beim Hinzufügen des Kunden';
        this.$emit('error', errorMessage);
      } finally {
        this.isSaving = false;
      }
    }
  }
};
</script>

<style scoped>
.list-item-hover {
  cursor: pointer;
  transition: background-color 0.2s;
}

.list-item-hover:hover {
  background-color: rgba(0, 0, 0, 0.04);
}
</style>